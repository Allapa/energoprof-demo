<?php

namespace App\Controllers;

use App\Models\Tag;
use Twig\Environment;
use App\Models\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ClientController
{
    protected $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function index()
    {
        $sort = $_GET['sort'] ?? 'asc';
        $clients = Client::orderBy('last_name', $sort)->get();
        echo $this->twig->render('clients/index.twig', [
            'clients' => $clients,
            'sort' => $sort == 'asc' ? 'desc' : 'asc'
        ]);
    }

    public function create()
    {
        $tags = Tag::all();
        echo $this->twig->render('clients/create.twig', ['path' => PACKAGE . "/clients/store", 'tags'=>$tags]);
    }

    public function store()
    {
        $client = new Client([
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'second_name' => $_POST['second_name'],
            'phone' => $_POST['phone'],
            'company_name' => $_POST['company_name'],
            'comment' => $_POST['comment'],
        ]);
        $client->save();
        if (isset($_POST['tags']))
            $client->tags()->attach($_POST['tags']);
        header("Location: " . PACKAGE);
        exit;
    }

    public function edit($id)
    {
        try{
            $client = Client::with('tags')->findOrFail($id);
        }catch(ModelNotFoundException $e){
            echo '404 Not Found';
            exit;
        }
        $tags = Tag::all();
        echo $this->twig->render('clients/edit.twig', [
                'client' => $client,
                'tags' => $tags,
                'path' => PACKAGE . "/clients/" . $client->id . "/update"
            ]
        );
    }

    public function update($id)
    {
        try{
            $client = Client::findOrFail($id);
        }catch(ModelNotFoundException $e){
            echo '404 Not Found';
            exit;
        }
        $client->first_name = $_POST['first_name'];
        $client->last_name = $_POST['last_name'];
        $client->second_name = $_POST['second_name'];
        $client->phone = $_POST['phone'];
        $client->company_name = $_POST['company_name'];
        $client->comment = $_POST['comment'];
        $client->save();
        $client->tags()->sync($_POST['tags'] ?? []);
        header("Location: " . PACKAGE);
        exit;
    }

    public function destroy($id)
    {
        $client = Client::find($id);
        $client?->delete();
        header("Location: " . PACKAGE);
        exit;
    }
}
