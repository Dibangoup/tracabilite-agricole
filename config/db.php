<?php 
  $con = mysqli_connect("localhost", "root", "", "tracabilite-agricole-db");

  if(!$con){
    echo "❌ Erreur de connexion : ". mysqli_connect_error();
  } else {
    echo "✅ Connexion réussie à la base de données 'tracabilite-agricole-db' !";
  }