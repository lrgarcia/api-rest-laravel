<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Category;
class PruebasController extends Controller
{
    public function index(){
        $animales = ["perro", "gato","tigre"];
        $titulo="animales";
        return view('pruebas.index',array(
            'animales'=>$animales,
            'titulo'=>$titulo
        ));
    }
    public function testOrm(){
       /* $posts=Post::all();
       //var_dump($posts);
        foreach($posts as $post){
            echo "<h1>".$post->title."</h1>";
            echo "<span style='color:gray;'>{$post->user->name} - {$post->category->name}</span>";
            echo "<p>".$post->content."</p>";
            echo "<hr>";
        }*/
        
        $categories= Category::all();
       // var_dump($categories);
    
         foreach($categories as $category){
            echo "<h1>".$category->title."</h1>";
            foreach($category->posts as $post){
                echo "<span style='color:gray;'>{$post->user->name} - {$post->category->name}</span>";
            echo "<p>".$post->content."</p>";
            }
          
        }
    die();
}
}
