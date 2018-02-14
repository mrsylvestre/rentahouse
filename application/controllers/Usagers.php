<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Usagers extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model("Usagers_model");
		$this->load->helper("url_helper");
		$this->load->library('session');
    //chargement de la librairie pour la validation du formulaire
		$this->load->library('form_validation');
		$this->load->helper('form');
		$this->load->helper('date');
	}

	/**
	 * Affiche la page d'accueil
	 */
	public function accueil() {
    // charger les vues
		$this->load->view("templates/header.php");
		$this->load->view("accueil/index");
		$this->load->view("templates/footer.php");
	}

  /*
   * connexion a la plateforme du site
  */
  public function connexion() {
  	$nomUsager = $this->input->post("nomUsager");
  	$motDePasse = $this->input->post("motDePasse");
  	if(isset($nomUsager) && isset($motDePasse)) {
  		if($nomUsager != "" && $motDePasse != "") {
        //vérifier les données usager dans le model usager
  			$resultat = $this->Usagers_model->verifier_usager($nomUsager, $motDePasse);
  			if($resultat) {
  				$utilisateur = array(
  					'username'  => $nomUsager
  				);
  				//Cree la session avec un username
  				$this->session->set_userdata($utilisateur);
  				$this->accueil();
  			}
  			else {
  				echo "echec";
  				$this->load->view("atterrissage/connexion-confirmation.php");
  			}
  		}
  	}
  }

  /*
   * Obtient un usager dans la base de donnees
  */
  public function obtenir_usager() {
  	$nomUsager = $this->input->post("nomUsager");
  	if(isset($nomUsager)) {
  		if($nomUsager != "") {
        // vérifier l'existance d'un usager dans le model usager
  			$usager = $this->Usagers_model->obtenir_usager($nomUsager);
  			if($usager)	{
  				$reponse = ["existe" => true];
  			} else {
  				$reponse = ["existe" => false];
  			}
  			header('Content-Type: application/json');
  			echo json_encode($reponse);
  		}
  	}
  }

  /*
   * Obtient un courriel dans la base de donnees
  */
  public function obtenir_courriel() {
  	$courriel = $this->input->post("courriel");
  	if(isset($courriel)) {
  		if($courriel != "")	{
        // vérifier l'existance d'un courriel dans la bd
  			$reponseCourriel = $this->Usagers_model->obtenir_courriel($courriel);
  			if($reponseCourriel) {
  				$reponse = ["existe" => true];
  			} else {
  				$reponse = ["existe" => false];
  			}
  			header('Content-Type: application/json');
  			echo json_encode($reponse);
  		}
  	}
  }

  /*
   * insertion d'un nouveau utilisateur dans la base de donnée
  */
  public function inscription() {
  	$nomUsager = $this->input->post("nomUsager");
  	$motDePasse = $this->input->post("motDePasse");
  	$courriel = $this->input->post("courriel");
  	$succes = true;

  	//S'il y a des donnees ne sont pas recues
  	if(!isset($nomUsager) || !isset($motDePasse) || !isset($courriel))	{
  		$succes = false;
  	} else {
      //S'il y a des donnees qui sont vides
  		if($nomUsager == "" || $motDePasse == "" || $courriel == "") {
  			$succes = false;
  		}	else {
        // ajout d'un usager dans le model usager
  			$resultat = $this->Usagers_model->ajouter_usager($nomUsager, $motDePasse, $courriel);
  			if(!$resultat) {
  				$succes = false;
  			}
  		}
  	}

  	if($succes) {
  		$data["erreur"] = false;
  		/*
      $this->load->library('email');
      $this->email->from('s.leila94@gmail.com', 'Leila');
      $this->email->to($courriel);
      $this->email->subject("Email de validation");
      $this->email->message("Merci pour votre inscription, votre compte sera bientôt validé par l'administrateur.");
      $this->email->send();
      */
    } else {
    	$data["erreur"] = true;
    }
    $this->load->view("atterrissage/inscription-confirmation.php", $data);
  }

}//Fin de la classe