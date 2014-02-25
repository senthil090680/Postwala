<!-- facebook starts here -->

        <link rel="stylesheet" href="<?=SITE_URL?>/style/facebook.css" type="text/css" media="screen, projection" />
        <script type="text/javascript">
            $(document).ready(function(){

                $("#facebookLink").click(function(){                    
                    $("#facebookForm").slideDown("slow");
		    $("#facebookLink").slideUp("slow");                    
                });
		
		$("#facebookClose").click(function(){
                        $("#facebookForm").slideUp("slow");
			$("#facebookLink").slideDown("slow");
                });
		
		    $("#facebookForm").slideDown("slow");
		    $("#facebookLink").slideUp("slow");  
                
            });
          </script>
	  
<div class="box">         
            <div id="facebookFormContainer"> 
                <div id="facebookForm">		   
			<div style="width:180px;padding:8px ;background-color:#3B5998;text-align:left;">
			   <div id="facebookClose" style="color:#FFF;font-size:13px;font-weight:bold;">  
		             <img src="<?=SITE_URL?>/images/collapse.jpg" border=0 alt="" />
			   </div>  
			   <div id="facebookLikeLink" style="color:#FFF;font-size:13px;font-weight:bold;">   
			     Like our facebook page
		           </div> 
			 </div>   
	                 <iframe src="http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fpages%2FPostwala%2F101435689939709&amp;width=200&amp;colorscheme=light&amp;connections=6&amp;stream=false&amp;header=false&amp;height=250" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:230px;" ></iframe>                    	                 

	         </div>  
		<div id="facebookLink">	</div>    
            </div>           
</div> 
<!-- facebook ends here -->  