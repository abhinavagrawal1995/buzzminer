<?php require_once 'db_connection.php'; ?>
<?php require_once 'functions.php'; ?>
<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/pure-min.css">
	<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/grids-responsive-min.css">
	<link rel="stylesheet" href="css/layouts/marketing.css">
	<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">

	<meta charset="UTF-8" />
	<title>Twitter Sentiment Analysis </title>
	<script src="Chart.js"></script>
	<style>
		body{
			padding: 0;
			margin: 0;
		}
		#canvas-holder{
			width:30%;
		}
	</style>


</head>

<body>

	<div class="header">
		<div class="home-menu pure-menu pure-menu-open pure-menu-horizontal pure-menu-fixed" style="text-align: center" >
			<a class="pure-menu-heading" href="">Buzz Miner</a>
		</div>
	</div>

	<div class="splash-container">
		<div class="splash">
			<h1 class="splash-head">Twitter Sentiment Analysis</h1>
			<p class="splash-subhead">

			</p>
			<p>
				<!-- <a href="http://purecss.io" class="pure-button pure-button-primary">Get Started</a> -->
			</p>
		</div>
	</div>

	<div class="content-wrapper">
		<div class="content">
			<h2 class="content-head is-center">Type your BUZZword and select how many Tweets to analyse:</h2>

			<div class="pure-g">
				<div class="l-box pure-u-1-1">

					<form method="POST" class="pure-form pure-form-stacked">
						<fieldset>
							<label>Keyword: </label> <input type="text" name="q" placeholder="e.g. Manipal"/>
							<label>Number of Tweets:</label>
							<select name="num_tweets">
								<option value="5" selected>5</option>
								<option value="20">20</option>
								<option value="40">40</option>
								<option value="60">60</option>
								<option value="80">80</option>
							</select>

							<label>Select a classifier:</label>
							 Custom trained Model using Naive Bayes (positive | negative)<input type="radio" name="classifier" value="custom" checked>
							Alchemy Deep Learning API (positive | negative | neutral)<input type="radio" name="classifier" value="alchemy" ><br>
						       
						</fieldset>
						<input type="submit" /> <br>
					</form>				
				</div>
			</div>
		</div>
		<div align="center"> <h2>
		<?php if(isset($_POST['q'])){
			echo '<h2 class="content-head is-center content">Here are the results for ' . $_POST['q'] . '</h2>';
		}?>
		</h2></div>
			<div id="canvas-holder" class="content" style="float:left">
			
			<h2>Most Positive Opinions</h2>
			<div id="pctnt"></div>
					
				<canvas id="chart-area" width="250" height="250"></canvas>
				
				
	
			</div>
			
			<div id="canvas-holder"  class="content" style="float:right" >
			
				<h2>Most Negative  Opinions</h2>
				<div id="nctnt"></div>
				
				
				<canvas id="chart-areaa" width="250" height="250"></canvas>

			</div>
			
			

			
		<div class="content">
		<div id="list" class="pure-u-1">



			<?php
			//ini_set('display_errors', 1);
			require_once 'alchemyapi.php';
			require_once 'Sentiment1.php';
			$custom_sent = new Sentiment1();
			$custom_sent -> train(19000); 
			$alchemyapi = new AlchemyAPI();
			/*use TwitterAPIExchange for handling calls */
			
			require_once('TwitterAPIExchange.php');
			/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
			$oauth_file = file_get_contents("oauthkeys.txt");
			$oauth_arr = explode("\n", $oauth_file);
			$settings = array(
				'oauth_access_token' => $oauth_arr[0],
				'oauth_access_token_secret' => $oauth_arr[1],
				'consumer_key' => $oauth_arr[2],
				'consumer_secret' => $oauth_arr[3]
				);
	//echo $_POST['classifier'];
			if ( isset($_POST['q'])){
		//$query = 'monday';
				$loc=array(
					'Asia'=>'34.0479,100.6197,10000mi',
					'Europe'=>'54.5260,15.2551,10000mi',
					'North America'=>'54.5260,-105.2551,10000mi',
					'South America'=>'-8.7832,-55.4915,10000mi',
					'Australia'=>'-25.2744,133.7751,10000mi',
					'Africa'=>'-8.7832,34.5085,10000mi',
					'Antarctica'=>'-82.8628,135.0000,10000mi'
					);
				$locl=array_values($loc);
				$locn = array_keys($loc);
				$pos_count= [0,0,0,0,0,0,0] ;
				$neg_count= [0,0,0,0,0,0,0] ;
				$neu_count= [0,0,0,0,0,0,0] ;
				$cust_pos=  [0,0,0,0,0,0,0] ;
				$cust_neg=  [0,0,0,0,0,0,0] ;
				
				for($i=0;$i<7;$i++)
				{
				$url = 'https://api.twitter.com/1.1/search/tweets.json';
				$x1 = " ";
				$y1 = "%20";
				$z1 = $_POST['q'];
				$qry= str_replace($x1,$y1,$z1);
				$getfield ='?lang=en&count=' . $_POST['num_tweets'] . '&geocode=' .$locl[$i]. '&q=' . $qry . '-filter:retweets';
				//$getfield = '?q=manipal';
				$requestMethod = 'GET';
				$twitter = new TwitterAPIExchange($settings);
				//var_dump($twitter);
				$tweets = json_decode($twitter->setGetfield($getfield)
					->buildOauth($url, $requestMethod)
					->performRequest());
				//var_dump($tweets);
				echo 'Region: <b>'.$locn[$i]. '</b> <br>';
				foreach ($tweets -> statuses as $t) {
					// foreach ($tweet as $t){
				//calculate the sentiment of each tweet
						if ($t -> text != ''){
							if($_POST['classifier']=='alchemy'){
								$response = $alchemyapi -> sentiment('text', $t -> text , null);				
								display_tweet($t, $response);
								// store_tweet_db($_POST['q'], $t->text, $response['docSentiment']['type'],$response);
								if ($response['docSentiment']['type']=='neutral'){
									$neu_count[$i]++;
								}
								if ($response['docSentiment']['type']=='positive'){
									$pos_count[$i]++;
								}
								if ($response['docSentiment']['type']=='negative'){
									$neg_count[$i]++;
								}
							} 
							
							else {
								$sentiment = $custom_sent ->classify($t->text);
								display_custom($t,$sentiment);
								if ($sentiment=='pos'){
									$cust_pos[$i]++;
								} else {
									$cust_neg[$i]++;
								}
							}
																		
						} 
					}
					echo '<br>';
					echo "Sentiment Scores Summary for ".$locn[$i].":". "&nbsp";
					if($_POST['classifier']=='alchemy')
					{
					echo 'positives: ' . $pos_count[$i] . '&nbsp';
					echo 'negatives: ' . $neg_count[$i] . '&nbsp';
					echo 'neutrals: ' . $neu_count[$i]. '<br>'; 
					}
					else if($_POST['classifier']=='custom')
					{
					echo 'positives: ' . $cust_pos[$i] . '&nbsp';
					echo 'negatives: ' . $cust_neg[$i] . '<br>';
					}
					echo '<hr>';
				}
			
			// To get the max positive and -ve sentiment continents	
				
			$maxpos=max($pos_count);
			$key = array_search($maxpos, $pos_count);
			$sneg=$neg_count[$key];
			$snu1=$neu_count[$key];
			$pregion=$locn[$key];
			
			
			$maxneg=max($neg_count);
			$key = array_search($maxneg, $neg_count);
			$spos=$pos_count[$key];
			$snu2=$neu_count[$key];
			$nregion=$locn[$key];
			
		
			
			$maxcpos=max($cust_pos);
			$key = array_search($maxcpos, $cust_pos);
			$scneg=$cust_neg[$key];
			$pcregion=$locn[$key];
			
			
			$maxcneg=max($cust_neg);
			$key = array_search($maxneg, $cust_neg);
			$scpos=$cust_pos[$key];
			$ncregion=$locn[$key];
			
			
			
			
		}	
			?>

		</div>
<!-- <div class="content-wrapper"> -->
	<!-- <div class="content"> -->
		
		<!-- <div class="pure-g content"> -->
			
			

			<script>
				var doughnutData = [
				{
					value: <?php echo ($_POST['classifier']=='alchemy' ? $sneg : $scneg); ?>,
					color:"#F7464A",
					highlight: "#FF5A5E",
					label: "Negative"
				},
				{
					value: <?php echo ($_POST['classifier']=='alchemy' ? $maxpos : $maxcpos); ?>,
					color: "#46BFBD",
					highlight: "#5AD3D1",
					label: "Positive"
				},
				{
					value: <?php echo $snu1;  ?>,
					color: "#949FB1",
					highlight: "#A8B3C5",
					label: "Neutral"
				}
				];
				var doughnutData1 = [
				{
					value: <?php echo ($_POST['classifier']=='alchemy' ? $maxneg : $maxcneg); ?>,
					color:"#F7464A",
					highlight: "#FF5A5E",
					label: "Negative"
				},
				{
					value: <?php echo ($_POST['classifier']=='alchemy' ? $spos : $scpos); ?>,
					color: "#46BFBD",
					highlight: "#5AD3D1",
					label: "Positive"
				},
				{
					value: <?php echo $snu2;  ?>,
					color: "#949FB1",
					highlight: "#A8B3C5",
					label: "Neutral"
				}
				];
				window.onload = function(){
					var ctx = document.getElementById("chart-area").getContext("2d");
					window.myDoughnut = new Chart(ctx).Doughnut(doughnutData, {responsive : true});
					
					var ctx1 = document.getElementById("chart-areaa").getContext("2d");
					window.myDoughnut1 = new Chart(ctx1).Doughnut(doughnutData1, {responsive : true});
					
					
					
				};
				document.getElementById("pctnt").innerText =  <?php echo '"'. ($_POST['classifier']=='alchemy' ? $pregion : $pcregion).'"'; ?>  ;
				document.getElementById("nctnt").innerText =  <?php echo '"'. ($_POST['classifier']=='alchemy' ? $nregion: $ncregion).'"'; ?>  ;
				
			
				
			</script>
			<!-- </div> -->
		</div>
	</div>
</div>
</div>
</body>
</html>
<?php
	//close db connection
if (isset($connection)) {
	mysqli_close($connection);
}
?> 