<?php

namespace Og\Cruid\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Og\Cruid\Facades\Cruid;

class CruidUserController extends CruidBaseController
{
    public function profile(Request $request)
    {
        $route = '';
        $dataType = Cruid::model('DataType')->where('model_name', Auth::guard(app('CruidGuard'))->getProvider()->getModel())->first();
        if (!$dataType && app('CruidGuard') == 'web') {
            $route = route('cruid.users.edit', Auth::user()->getKey());
        } elseif ($dataType) {
            $route = route('cruid.'.$dataType->slug.'.edit', Auth::user()->getKey());
        }

        return Cruid::view('cruid::profile', compact('route'));
    }

    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        if (Auth::user()->getKey() == $id) {
            $request->merge([
                'role_id'                              => Auth::user()->role_id,
                'user_belongstomany_role_relationship' => Auth::user()->roles->pluck('id')->toArray(),
            ]);
        }

        return parent::update($request, $id);
    }
}
