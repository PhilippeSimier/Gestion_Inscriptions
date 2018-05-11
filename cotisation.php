<?php
//------------------------------------------------------------
// Module pour d�terminer le montant de la cotisation
// doit �tre modifi� en fonction des r�gles adopt�es
//------------------------------------------------------------
 
 function prix_engagement($bdd, $id_epreuve){
   // r�gle 1 engagement est gratuit pour les licenci�s Sarthois
   // les NL FFA n'ont pas de num�ro de club
  
   $sql = "SELECT prix FROM `cross_route_epreuve` WHERE `id_epreuve`='".$id_epreuve."'";
   $stmt = $bdd->query($sql);
   $epreuve = $stmt->fetchObject();
   return $epreuve->prix;
 }
 
 // le prix des prestations compl�mentaires est m�moris� dans prix de la table comp�tition
 function prix_cotisation($bdd, $noclub,$nomcourse,$competition,$nb,$regle){
    // prestation compl�mentaire (repas transport ...) A finir
    $prestation = 0;
    return $prestation + prix_engagement($bdd, $noclub, $nomcourse, $competition, $regle);

 }
 
?>
