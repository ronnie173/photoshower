<?php

namespace App\Http\Controllers;

use App\Album;
use Illuminate\Http\Request;

class AlbumsController extends Controller
{
    public function index()
    {
        $albums = Album::with('Photos')->get();
        return view('albums.index')->with('albums',$albums);
    }

    public function create()
    {
        return view('albums.create');
    }

    //upload images
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'cover_image' => 'image|max:1999',
        ]);

        //Get file name with extension
        $fileNameWithExt = $request->file('cover_image')->getClientOriginalName();

        //Get just the file name
        $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);

        //Get extension
        $extension = $request->file('cover_image')->getClientOriginalExtension();

        //create the new file name
        $fileNameToStore = $fileName . '_' . time() . '.' . $extension;
        //upload image
        $path = $request->file('cover_image')->storeAs('public/album_covers', $fileNameToStore);

        //create album
        $album = new Album;
        $album->name = $request->input('name');
        $album->description = $request->input('description');
        $album->cover_image = $fileNameToStore;
        $album->save();

        return redirect('/albums')->with('success','Album Created');

    }

    public function show($id){
        $album = Album::with('Photos')->find($id);
        return view('albums.show')->with('album',$album);
    }
}
