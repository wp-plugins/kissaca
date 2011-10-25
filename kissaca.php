<?php
    /*
    Plugin Name: Kıssaca
    Plugin URI: http://www.ozqan.com
    Description: Görünmesini istediğiniz yerde yazdığınız kıssaca yazıları görüntüler.
    Author: OzqaN
    Version: 1.0
    Author URI: http://www.ozqan.com/
    */
    /* Create Database - Veritabani Olusturma */
    $wpdb->kissaca = $wpdb->prefix . 'kissaca';
    /* Tablomuzun adı kissaca olup, bunu $wpdb isimli
    WP'nin veritabanı sınıfına $wpdb->kissaca olarak tanıtıyoruz*/
    function kissaca_kurulum() {
    /* Kurulum işlemini yapacak olan fonksiyonumuz, ismini istediğiniz gibi verebilirsiniz*/
        global $wpdb;
    /* $wpdb adlı  WP'nin veritabanı sınıfını fonksiyonumuza çağırıyoruz. Fonksiyonlarımızda veritabanı işlemleri yapmak için bunu yapmamız gerekiyor.*/
        $db_sql="CREATE TABLE IF NOT EXISTS `$wpdb->kissaca` (
      `id` bigint(20) NOT NULL auto_increment,
      `kissacametin` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
	  `tarih` date NOT NULL default '00-00-0000',
       PRIMARY KEY  (`id`)
    )";

        $wpdb->query($db_sql);
    }
    if (isset($_GET['activate']) && $_GET['activate'] == 'true') {

        add_action('init', 'kissaca_kurulum');
    }
	//Admin panelinde ayarlar menüsünüe submenu olarak eklenmesini sağlıyoruz...
    add_action('admin_menu', 'yonetime_ekle');
    function yonetime_ekle() {
        add_submenu_page('options-general.php', 'Kissaca', 'Kissaca', 10, __FILE__, 'kissaca_menu');
    }
    function kissaca_menu() {
        global $wpdb;
//Silme ekleme ve düzenleme gibi olayları yapacak olan fonksiyonlarımızı GET ve POST olarak gelen verilerle eşleştiriyoruz...
        echo '<div class="wrap">';
        if ($_POST['islem']== 'ekle') { kissaca_ekle (); }
        if ($_GET['islem']== 'sil') { kissaca_sil (); }
        if ($_GET['islem']== 'duzenle') { kissaca_duzenle (); }
//Veritabanımızdan verileri çekip listeliyoruz                    
        $sorgu = "SELECT * FROM $wpdb->kissaca order by id desc";
        $sonuclar = $wpdb->get_results($sorgu);

         if ($sonuclar) {
            echo "<strong>Kıssaca Yazılarım:</strong>";
            echo "<ol>";
            foreach ($sonuclar as $sonuc) {
                $metin=stripslashes($sonuc->kissacametin);
				$tarih=stripslashes($sonuc->tarih);
        echo "<li> <code>".$tarih."</code>";  echo $metin;
        echo "-[<a href='".$_SERVER['PHP_SELF']
          ."?page=kissaca/kissaca.php&islem=sil&silno=".$sonuc->id."'>Sil</a>]";
        echo "-[<a href='".$_SERVER['PHP_SELF']
          ."?page=kissaca/kissaca.php&degistir=".$sonuc->id."'>Düzenle</a>]</li>";
            }
            echo "</ol>";
//Eğer bir kısaca yazımız yok ise yani sorgu boş döner ise yazınız bulunmuyor diye bir uyarı vermesini sağlıyoruz...	
        } else { echo "Kissaca yazınız bulunmuyor!"; }
        if (!isset($_GET['degistir'])) {
//işlemlerimizde kullanacağımız tarihi $tarihyaz değişkenine atıyoruz
		$tarihyaz = date("Y.m.d");
	 ?>
    <form action="<?php $_SERVER['PHP_SELF'] ?>?page=kissaca/kissaca.php" method="post">
            <fieldset>
            <table width="400">
                <tr><td width="400"><b>Yeni Kıssaca</b></td></tr>
                <tr><td><textarea name="metin" id="metin" cols="45" rows="6" tabindex="4"></textarea></td></tr>
                <tr>
                  <td><label>Tarih:
                      <input type="text" name="tarih" id="tarih" value="<?php echo $tarihyaz; ?>" />
                  </label></td>
                </tr>
                <tr><td><input type="submit" name="submit" value="Kıssaca Ekle" class="button" tabindex="5" /></td></tr>
            </table>
                <INPUT TYPE="hidden" name="islem" value="ekle"></p>
            </fieldset>
        </form>
    <?php
        }

        else
        {
          $sql = "SELECT * FROM $wpdb->kissaca where id=".$_GET['degistir'];
          $sonuclartekkissaca = $wpdb->get_results($sql);
          if ($sonuclartekkissaca) {
        foreach ($sonuclartekkissaca as $sonuctekkissaca) {
    ?>
   
         <form action="<?php $_SERVER['PHP_SELF'] ?>?page=kissaca/kissaca.php" method="Post">
                <table width="400">
                <tr><td><b>Kıssaca Düzenle</b></td></tr>
                <tr><td><textarea name="metin" id="metin" cols="45" rows="6" tabindex="4"><?php echo $sonuctekkissaca->kissacametin; ?></textarea></td></tr>
                <tr>
                  <td>
                    <label>Tarih:
                      <input name="tarih" type="text" id="tarih" value="<?php echo $sonuctekkissaca->tarih; ?>" />
                  </label></td>
                </tr>
                <tr><td><input type="submit" name="submit" value="Kıssaca Düzenle" class="button" tabindex="5" /></td></tr>
            </table>
                <INPUT TYPE="text" name="id" value="<?php echo $sonuctekkissaca->id; ?>">
                <INPUT TYPE="hidden" name="islem" value="duzenle">
        </form>
    <?php
     
    } } }
            echo "</div>";
    } // Fonksiyonun sonu

    //Yeni - New
    function kissaca_ekle (){
        global $wpdb ;
        $metin=$wpdb->escape($_POST['metin']);
		$tarih=$wpdb->escape($_POST['tarih']);
        $sql= "INSERT INTO ".$wpdb->kissaca." VALUES (NULL,'".$metin."','".$tarih."')";
        $wpdb->query($sql);
		if($sql) { echo "Kıssaca Yazınız Basarili";} else { echo "Kıssaca Yazınız Basarisiz"; } 
    //Aşağıdaki koddaki "updated fade" klası uyarı mesajlarına fade efekti uyguluyor.
    ?>
        <div id="message" class="updated fade"><p>Yeni kıssaca eklendi! </p></div>
    <?php
    }
     
    //Sil-Delete
    function kissaca_sil () {
         global $wpdb ;
         $sql="DELETE FROM ".$wpdb->kissaca." WHERE id='".(int) $_GET['silno']."'";
         $sonuc=$wpdb->query($sql);?>
        <div id="message" class="updated fade"><p><strong>Kıssaca silindi!</strong></p></div>
    <?php
    }
    //Edit - Düzenle
    function kissaca_duzenle () {
       global $wpdb ;
       $metin=$wpdb->escape($_POST['metin']);
	   $tarih=$wpdb->escape($_POST['tarih']);
       $sql="UPDATE ".$wpdb->kissaca." SET kissacametin='".$metin."',tarih='".$tarih."' where id='". (int) $_POST['id']."' ";
       $wpdb->query($sql);?>
       <div id="message" class="updated fade"><p><strong>Kıssaca düzenlendi!</strong> </p></div>
  
    
    <?php
        }
	 //css wp headere ekliyoruz...
function css_head()
{
    echo '<link rel="stylesheet type="txt/css"  href="'.get_settings('siteurl').'/wp-content/plugins/kissaca/style.css"/>
<script type="text/javascript" src="'.get_settings('siteurl').'/wp-content/plugins/kissaca/quickpager.jquery.js"></script>
<script type="text/javascript">
        jQuery.noConflict();
    </script>
<script type="text/javascript">
/* <![CDATA[ */

jQuery(document).ready(function() {
	
	
	jQuery("ul.paging").quickPager();
	
	
	jQuery("ul.paging2").quickPager({pagerLocation:"both"});
});

/* ]]> */
</script>';
    
}
add_action('wp_head', 'css_head');


    // Ana fonksiyon - Main Function
    function kissaca(){
		$url = site_url('/wp-content/plugins/kissaca/', 'http');
		echo "<div class='titlekissaca'>OzqaN'dan Kıssaca</div>";
		echo "<ul class='paging'>";
		global $wpdb;
        $sorgu = "SELECT * FROM $wpdb->kissaca ORDER BY ID DESC LIMIT 30 ";
        $sonuclar = $wpdb->get_results($sorgu);
        if ($sonuclar) {
           foreach ($sonuclar as $sonuc) { 
		   $metin=stripslashes($sonuc->kissacametin);
		   echo"<li><img src='$url./ifade.gif' width='25'/>$metin<div class='datekissaca'>$sonuc->tarih</div></li>"; }
                }
  echo "</ul>";
   }  


//widget sidebar
register_sidebar_widget('Kıssaca', 'kissaca');

?>