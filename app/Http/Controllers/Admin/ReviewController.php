<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('comment', 'LIKE', '%' . $request->search . '%')
                  ->orWhereHas('user', function($u) use ($request) {
                      $u->where('name', 'LIKE', '%' . $request->search . '%');
                  })
                  ->orWhereHas('product', function($p) use ($request) {
                      $p->where('name', 'LIKE', '%' . $request->search . '%');
                  });
            });
        }

        $reviews = $query->latest()->paginate(15)->appends($request->all());

        // Stats
        $stats = [
            'total' => Review::count(),
            'pending' => Review::where('status', 'pending')->count(),
            'approved' => Review::where('status', 'approved')->count(),
            'rejected' => Review::where('status', 'rejected')->count(),
            'avg_rating' => Review::where('status', 'approved')->avg('rating') ?? 0,
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    public function show($id)
    {
        $review = Review::with(['user', 'product', 'order'])->findOrFail($id);
        return view('admin.reviews.show', compact('review'));
    }

    public function approve($id)
    {
        $review = Review::findOrFail($id);
        $review->status = 'approved';
        $review->save();

        return redirect()->back()->with('success', 'Review approved successfully!');
    }

    public function reject($id)
    {
        $review = Review::findOrFail($id);
        $review->status = 'rejected';
        $review->save();

        return redirect()->back()->with('success', 'Review rejected successfully!');
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        
        // Delete images
        if ($review->images) {
            foreach ($review->images as $image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($image);
            }
        }
        
        $review->delete();
        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully!');
    }

    public function bulkApprove(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No reviews selected']);
        }

        Review::whereIn('id', $ids)->update(['status' => 'approved']);
        return response()->json(['success' => true, 'message' => 'Reviews approved successfully']);
    }

    public function bulkReject(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No reviews selected']);
        }

        Review::whereIn('id', $ids)->update(['status' => 'rejected']);
        return response()->json(['success' => true, 'message' => 'Reviews rejected successfully']);
    }
}