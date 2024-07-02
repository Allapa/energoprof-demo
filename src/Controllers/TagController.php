<?php

namespace App\Controllers;

use App\Models\Tag;
use Twig\Environment;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TagController
{
    protected $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function index()
    {
        $tags = Tag::all();
        echo $this->twig->render('tags/index.twig', [
            'tags' => $tags,
        ]);
    }

    public function create()
    {
        echo $this->twig->render('tags/create.twig', ['path' => PACKAGE . "/tags/store"]);
    }

    public function store()
    {
        $tag = new Tag(['name' => $_POST['name']]);
        $tag->save();
        header('Location: '.PACKAGE.'/tags');
        exit;
    }

    public function edit($id)
    {
        try{
            $tag = Tag::findOrFail($id);
        }catch(ModelNotFoundException $e){
            echo '404 Not Found';
            exit;
        }
        echo $this->twig->render('tags/edit.twig', ['tag' => $tag, 'path' => PACKAGE . "/tags/" . $tag->id . "/update"]);
    }

    public function update($id)
    {
        try{
            $tag = Tag::findOrFail($id);
        }catch(ModelNotFoundException $e){
            echo '404 Not Found';
            exit;
        }
        $tag->name = $_POST['name'];
        $tag->save();
        header('Location: '.PACKAGE.'/tags');
        exit;
    }

    public function destroy($id)
    {
        $tag = Tag::find($id);
        $tag?->delete();
        header('Location: '.PACKAGE.'/tags');
        exit;
    }
}