<html>
    <head>
       <meta charset="utf-8">
        <!-- importer le fichier de style -->
        <link rel="stylesheet" href="funding.css" media="screen" type="text/css" />
    </head>
    <body>
      <div class=container>
      <form action="" method="post">
       <p>Êtes-vous inscrit à pole emploi ? : <input type="checkbox" name="inscrit_pe"></p>
       <p>Votre date de naissance : <input type="date" name="date_naiss"></p>
       <p>Le numéro Intercarif de votre formation : <input type="text" name="num_formation" /></p>
       <p><input type="submit" value="OK"></p>
      </form>

      <?php
      error_reporting(0);
if(isset($_POST["num_formation"]) && !empty($_POST["date_naiss"])){
      $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://entreprise.pole-emploi.fr/connexion/oauth2/access_token?realm=/partenaire',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => 'grant_type=client_credentials&client_id=PAR_1c_cb1d7d039427037a6ac45ebc8fc06b0a6fc22485a493fe5d9708a2db8fdc7b4a&client_secret=268272134b9c5985ea8b84562199c96964ddfe82c4c25831619073dbfb1c2ac2&scope=api_simulateurfinancementv1%20application_PAR_1c_cb1d7d039427037a6ac45ebc8fc06b0a6fc22485a493fe5d9708a2db8fdc7b4a',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
            'Cookie: BIGipServerVS_IW_PO002-VIPA-00PX20b_HTTPS.app~POOL_IW_PO002-00PX20b_HTTPS_SO007_SFPSN_13=!ELm9gk7Y5DJuBXYtSoEWKFKmyo2ysvdnKGdOL2uQHmmUtjOLG3pdTAjrNjKSMdYJyFhJe1OwiSJTGKs=; TS0188135e=01b3abf0a2993d481a337867346ccf4499c894dec4e0331566a572e693a2c11e47582a15cd1265343cf8c0ba30aa73ff0d3c87072f'
          ),
        ));

      $response = curl_exec($curl);
      curl_close($curl);

      $arrtok = (array) json_decode($response);
      $token = $arrtok["access_token"];

      $num_f = htmlspecialchars($_POST['num_formation']);
      $date_naiss = htmlspecialchars($_POST["date_naiss"]);
      $date_naiss = strtotime($date_naiss);
      $date_naiss = date('Y-d-m',$date_naiss);
      $inscrit_pe = false;
      try{
            $inscrit_pe = htmlspecialchars($_POST["inscrit_pe"]);	
      }catch(Exception $e){

      }

      if($inscrit_pe == true){
      	$inscrit_pe = "true";
      }
      else{
      	$inscrit_pe = "false";
      }

      $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.emploi-store.fr/partenaire/simulateurfinancement/v1/financement?eligible=true&explain=false',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
          "beneficiaire": {
              "inscrit_pe": '.$inscrit_pe.',
              "naissance": 	"'.$date_naiss.'"
          },
          "formation": {
              "numero": "'.$num_f.'"
          }
      }',
        CURLOPT_HTTPHEADER => array(
          'Authorization: Bearer '.$token,
          'Content-Type: text/plain'
        )
      ));


      $response = curl_exec($curl);


      curl_close($curl);
      $jarr = (array) json_decode($response);
      if ( array_key_exists('financements', $jarr) == true )
      {
        $jstr = $jarr['financements'];
        if ( array_key_exists(0, $jstr) == true )
                {
                    $jj = (array)$jstr[0];
                    if ( array_key_exists('intitule', $jj) == true )
                            {
                                echo "<h4>Vous êtes éligibles aux financements suivants pour votre formation :</h4>";
                                echo $jj['intitule'];
                            }
                    else{
                      echo "<p>Une erreur est survenue</p>";
                    }
                }
        else{
          echo "<p>Vous n'êtes malheureusement pas éligible à un financement :(</p>";
        }
      }
      else{
        echo "<p>Une erreur est survenue1</p>";
      }
}
      ?>
    </div>
    </body>
</html>

