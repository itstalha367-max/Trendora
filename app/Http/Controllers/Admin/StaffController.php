<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\User;use App\Models\AdminRole;use Illuminate\Http\Request;use Illuminate\Support\Facades\Hash;use Illuminate\Validation\Rule;
class StaffController extends Controller {
 public function index(){ $staff=User::where('role','admin')->with('adminRole')->latest()->paginate(15);$roles=AdminRole::withCount('users')->orderBy('name')->get();return view('admin.staff.index',compact('staff','roles'));}
 public function create(){return view('admin.staff.form',['staffUser'=>new User,'roles'=>AdminRole::orderBy('name')->get()]);}
 public function store(Request $r){$d=$this->valid($r);$d['role']='admin';$d['password']=Hash::make($d['password']);User::create($d);return redirect()->route('admin.staff.index')->with('success','Staff account created.');}
 public function edit(User $staffUser){abort_unless($staffUser->role==='admin',404);return view('admin.staff.form',compact('staffUser')+['roles'=>AdminRole::orderBy('name')->get()]);}
 public function update(Request $r,User $staffUser){abort_unless($staffUser->role==='admin',404);$d=$this->valid($r,$staffUser);if(empty($d['password']))unset($d['password']);else $d['password']=Hash::make($d['password']);$staffUser->update($d);return redirect()->route('admin.staff.index')->with('success','Staff account updated.');}
 public function destroy(User $staffUser){if($staffUser->id===auth()->id())return back()->with('error','You cannot delete your own admin account.');abort_unless($staffUser->role==='admin',404);$staffUser->delete();return back()->with('success','Staff account removed.');}
 private function valid(Request $r,?User $u=null){return $r->validate(['name'=>'required|string|max:120','email'=>['required','email',Rule::unique('users','email')->ignore($u?->id)],'phone'=>'nullable|string|max:40','admin_role_id'=>'nullable|exists:admin_roles,id','password'=>[$u?'nullable':'required','string','min:8','confirmed']]);}
}
