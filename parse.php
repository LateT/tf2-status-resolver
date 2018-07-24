<html>
<link rel="stylesheet" href="tyyli.css">
<td>
  <th>Avatar</th>
  &nbsp&nbsp&nbsp&nbsp<th>Etf2l name</th>
  &nbsp&nbsp&nbsp&nbsp<th>Country</th>
  &nbsp&nbsp&nbsp&nbsp<th>Etf2l Ranks</th>
<?php
session_start();
echo "<title>Please wait..</title>";
include_once("simple_html_dom.php");
if(isset($_POST["status"])){
$data1=$_POST["status"];
preg_match_all("/(\[U\:1\:.*?\])/",$data1,$data2);
$count1=count($data2[0])-1; // counting array length, -1 becouse last row is empty
$profiles3 = array(); // array for steamid3's
$profiles64= array(); // array for steamid64's
$etf2lname= array(); // array for etf2l names
$etf2lrank=array(array()); //array for etf2l ranks
$steamavatar=array(); // array for steam avatars
$countries=array(); // etf2l countries
$ugcrank=array(array()); // ugc ranks array
$etf2lmax1=0; // used to count max amount of columns needed for saving ranks to mysql..
for($madirl=0;$madirl<=$count1;$madirl++) { // looping thru every array row to get all information
  $profiles3[$madirl]=$data2[0][$madirl]; // storing id3's incase its needed @ somepoint
  $url1="https://api.steamid.eu/convert.php?api=INSERT_STEAMID_EU_API_KEY_HERE&input=".$profiles3[$madirl]; // request url
  $content=str_get_html(file_get_contents($url1)); // getting data from the api
  $p2=preg_match_all("/\<steamid64\>(.*?)\</",$content,$output2); // match id64 from api
  $profiles64[$madirl]=$output2[1][0]; // store id64's
  $url2="http://api.etf2l.org/player/".$profiles64[$madirl].".xml"; // etf2l api url
  $content2=str_get_html(@file_get_contents($url2)); // requesting api..
  if ($content2){ // checking if page exists.
  $p3=preg_match_all("/\<player\sname\=\"(.*?)\"/",$content2,$output3);
  $json1=json_encode($output3);// encoding to json becouse random array lengths are bs
  $p3=preg_match_all("/\,\[\"(.*?)\"\]/",$json1,$output4);
  $etf2lname[$madirl]=$output4[1][0];
  $url3="http://api.etf2l.org/player/".$profiles64[$madirl]."/results.xml?since=0"; // getting etf2l match results
  $content3=str_get_html(file_get_contents($url3));
  $p4=preg_match_all("/\<division\sname\=\"(.*?)\"/",$content3,$output5);
  $count2=count($output5[1]);
  for($late=0;$late<=$count2;$late++) { // looping thru ranks n creating array inside array wich holds the ranks
    if (isset($output5[1][$late])) {
    $etf2lrank[$madirl][$late]=$output5[1][$late];


  }
  }
  $p5=preg_match_all("/<player\sname\=\".*?\"\sbans\=\".*?\"\scountry\=\"(.*?)\"/",$content2,$output7);
  if (isset($output7[1][0])==!FALSE) {
  $countries[$madirl]=$output7[1][0];
}
}
$url4="http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=INSERT_STEAM_API_KEY_HERE&steamids=".$profiles64[$madirl]."&format=json";
$content4=str_get_html(file_get_contents($url4));
$p6=preg_match_all("/\"avatarmedium\"\:\"(.*?)\"/",$content4,$output6); // getting url for steam profile image..
$steamavatar[$madirl]=$output6[1][0];
// ugc api starts here
$url05="https://www.ugcleague.com/api/api.php?key=INSERT_UGC_API_KEY_HERE&exists=".$profiles64[$madirl];
$content05=str_get_html(file_get_contents($url05));
$p05=preg_match("/\{\"exists\":true\}/",$content05); // if player has ugc continue gathering data..
if ($p05!==FALSE) {
  $url005="https://www.ugcleague.com/api/api.php?key=INSERT_UGC_API_KEY_HERE&player=".$profiles64[$madirl];
  $content005=str_get_html(@file_get_contents($url005));
  if ($content005) { // Checking if player has ugc page again, just incase. ( error could reveal api key)
  $p005=preg_match_all("/division\"\:\"(.*?)\"/",$content005,$output005);
  $countarray005=count($output005[1]); // get amount of ranks to save..
  for($num005=0;$num005<$countarray005;$num005++) { // grinding thru ugc ranks array n saving em
    $ugcrank[$madirl][$num005]=$output005[1][$num005];
  }
}

} // end of ugc api

// if (isset($etf2lrank[$madirl])!==FALSE) { // used in saving process..
// $etf2lmax2 = count($etf2lrank[$madirl]);
// if ($etf2lmax1<$etf2lmax2) { // getting highest amount of ranks played in to create enough columns
// $etf2lmax1 = $etf2lmax2;
//
// }
// } // after checking if player has etf2l rank..

// starting to show results..
echo "<div class='player'>";
if(isset($steamavatar[$madirl])!==FALSE){
echo "<tr> <a href='https://steamcommunity.com/profiles/".$profiles64[$madirl]."'><img src='".$steamavatar[$madirl]."'></a></tr>";
}
if(isset($etf2lname[$madirl])!==FALSE) {
echo "<tr><a href='http://etf2l.org/search/".$etf2lname[$madirl]."/'>&nbsp".$etf2lname[$madirl]."</a>&nbsp|</tr>";
}
else {
  echo "-";
}
if(isset($countries[$madirl])!==FALSE){
echo "<tr>&nbsp&nbsp&nbsp&nbsp&nbsp".$countries[$madirl]."&nbsp|</tr>";
}
if(isset($etf2lrank[$madirl][0])) {
  $c009=count($etf2lrank[$madirl]);
  echo "<tr>&nbsp&nbsp&nbsp&nbsp";
  for($f009=0;$f009<$c009;$f009++) {
    echo $etf2lrank[$madirl][$f009].",&nbsp";
    if($f009==($c009-1)){
      echo "&nbsp|</tr>";
    }
  }
}
if (isset($ugcrank[$madirl][0])) {
  $c0019=count($ugcrank[$madirl]);
  echo "<tr>&nbsp&nbsp&nbsp&nbsp";
  for($f0019=0;$f0019<$c0019;$f0019++) {
    echo $ugcrank[$madirl][$f0019].",&nbsp";
    if($f0019==($c0019-1)){
      echo "&nbsp|</tr>";
    }
  }
}


} // end of collecting player data

} // end of post status check
else {
  echo "<script> location.href='index.php';</script>"; // if no post received, redirecting back to submitting page
}
?>
</td>
