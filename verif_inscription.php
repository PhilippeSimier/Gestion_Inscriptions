<?php
	if (!is_readable('administration/en_tete.html'))  die ("fichier non accessible");
	@readfile('administration/en_tete.html') or die('Erreur fichier');
?>	
		
     
			<div id="contenu" style="min-height:500px;">
				<h2>Un simple clic <small>pour vérifier l'état de votre inscription !</small></h2>
				<div class="row">
					<div class=" col-md-6"> 
						Nom (ou début du nom)  ou % pour obtenir la liste complète
					</div>
					<div class=" col-md-6">
						<form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST" name="verification">
							<input  class="normal" name="nom"  placeholder="Nom"  required />
							<button  class="btn btn-info" type="submit">
								<span class="glyphicon glyphicon-search"></span> Rechercher
							</button>
							
						</form>
					</div>
				</div>
				<div class="row">
				 <p> </p>
				</div>
				
			
			<?php
            if (isset($_POST['nom'])&&($_POST['nom']!="")) {
				
				require_once('definitions.inc.php');
				// connexion à la base
				$bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);
				$sql = "SELECT `dossard`,`nolicence`,`cross_route_engagement`.`nom`,`prenom`,`categorie`,`nomcourse`,`competition`.`date`,`certifmedicalfourni`,`cotisationpaye`"
                  . "FROM `competition`,`cross_route_engagement`\n"
                  
                  . "WHERE cross_route_engagement.competition = competition.nom";
				$sql .= " AND cross_route_engagement.nom LIKE '".$_POST['nom']."%'";

				$stmt = $bdd->query($sql);
				$trouve=false;

				echo '<div class="table-responsive"><table class="table table-striped table-condensed">';
				echo "<tr><th>Dossard</th><th>N° licence</th><th>Nom</th><th>Prénom</th><th>Catégorie</th><th>Course</th><th>Date</th><th>Etat</th></tr>";
				while ($engagement = $stmt->fetchObject()){

					echo "<tr>";
					echo "<td>".$engagement->dossard."</td>";
					echo "<td>".$engagement->nolicence."</td>";
					echo "<td>".$engagement->nom."</td>";
					echo "<td>".$engagement->prenom."</td>";
					echo "<td>".$engagement->categorie."</td>";
					echo "<td>".$engagement->nomcourse."</td>";
					echo "<td>".date("j M Y",strtotime($engagement->date))."</td>";
					echo "<td>";
					$complet = ($engagement->nolicence || $engagement->certifmedicalfourni=="oui") && $engagement->cotisationpaye=="oui";
					if ($complet) echo "<span style=\"color:#2EBA0E\">- OK</span>";
					else {   echo "<span style=\"color:#F00\">";
                            if (!$engagement->nolicence && $engagement->certifmedicalfourni=="non") echo "- Certificat médical ";
                            if ($engagement->cotisationpaye=="non") echo "- Paiement ";
                            echo "</span>";
                        }
					$trouve=true;
				}
				echo "</td></tr></table></div>";
				if (!$trouve) echo "<p>Aucun engagement pour ".$_POST['nom']. " !</p>";
                                    	
			}
			?>
			</div>
		<?php
			 @readfile('administration/pied_de_page.html') or die('Erreur fichier');
		?>
		
