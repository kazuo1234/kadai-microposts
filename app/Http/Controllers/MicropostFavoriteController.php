<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class MicropostFavoriteController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $micropost_id)
    {
        \Auth::user()->registerFavorite($micropost_id);
        return redirect()->back();
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param $micropost_id
	 * @return \Illuminate\Http\Response
	 */
    public function destroy($micropost_id)
    {
        \Auth::user()->deRegisterFavorite($micropost_id);
        return redirect()->back();
    }
}
