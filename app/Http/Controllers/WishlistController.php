<?php

namespace App\Http\Controllers;

use App\Models\Watch;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $wishlists = $request->user()
            ->wishlists()
            ->with(['watch.brand', 'watch.images'])
            ->latest()
            ->get();

        return view('pages.wishlist', [
            'wishlists' => $wishlists,
        ]);
    }

    public function toggle(Request $request, Watch $watch): RedirectResponse
    {
        $wishlist = Wishlist::where('user_id', $request->user()->id)
            ->where('watch_id', $watch->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $status = 'removed';
        } else {
            Wishlist::create([
                'user_id' => $request->user()->id,
                'watch_id' => $watch->id,
            ]);
            $status = 'added';
        }

        return back()->with('wishlist_status', $status);
    }
}
