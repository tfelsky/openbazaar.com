
		
		<div class="Rectangle-10 clearfix">
			<div class="Page-Sub-Content">
				<div class="OB1">OB1</div>
				<div class="Search-Bar clearfix">
					<form action="javascript:void();" onsubmit="location.href='/discover/results/'+document.getElementById('frm-search-input').value;">
					<div class="Search-Bar-Box-Left">
						<div class="Rectangle-6">
							<img src="https://ob1.io/images/logo.png" class="logo"/>
						</div>
						<input id="frm-search-input" type="text" class="Search-OB1" placeholder="Search OB1..."/>
						
					</div>
					<button class="Search-Button" type="submit">
						<div class="Search">Search</div>
					</button>
					</form>
				</div>
				
				<div class="Suggestions-Box clearfix">
					<div class="lbl clearfix">Suggestions:</div> <a href="/discover/results/electronics">electronics</a> <a href="/discover/results/games">games</a> <a href="/discover/results/books">books</a> <a href="/discover/results/movies">movies</a> <a href="/discover/results/health">health</a> 
				</div>
				
				<div class="Line"></div>								
				
				
				<?php foreach($categories as $category) { ?>
					<h1><?=ucwords($category)?></h1>
				
					<div class="Main-Discover-Body">							
					<?php						
					$i = 0;
					
					$listings = $search_results[$category];
					
					foreach($listings as $listing) { 	
						
						$verified = false;

							foreach($listing->relationships->moderators as $mod) {
								foreach($verified_mods as $vermod) {
									if($mod == $vermod->peerID) {
										$verified = true;
										break;
									}
								}
								if($verified) {
									break;
								}
							}
						$listing->has_verified_mod = $verified;								
					?>

						<div class="Discover-Body-Listing-Box" onclick="location.href='/store/<?=$listing->relationships->vendor->data->peerID?>/<?=$listing->data->slug?>';">
							<div class="Discover-Body-Listing-Box-Photo" style="background-image: url('https://gateway.ob1.io/ob/images/<?=$listing->data->thumbnail->small?>');">
								<?php if($listing->has_verified_mod) { ?>
								<div class="verified-mod-badge" style="float:left;cursor:pointer;background-position: center center;width:36px;height:36px;background-size:24px 24px; background-repeat: no-repeat;background-image: url(https://search.ob1.io/images/verified_moderator_badge_tiny.png), url('../imgs/verifiedModeratorBadgeDefault-tiny.png');">
									
									<div class="verified-mod-tip hidden up-arrow" style="width:250px">
										<div style="margin-left:auto;margin-right:auto;text-align: center;display: table">
											<img src="https://search.ob1.io/images/verified_moderator_badge_tiny.png" width=24 style="width:24px;text-align:right;display: table-cell;vertical-align: middle; " />
											<span style="vertical-align: middle;display: table-cell">Verified Moderator</span>
										</div>
										<p style="font-size:15px;">Purchase this listing with a trusted moderator, verified by OB1. <br/> <a href="https://ob1.io/verified-moderators.html">Learn more</a></p>
									
									</div>
									
									
								</div>																											
								<?php } ?>
								
								<?php if(isset($listing->data->freeShipping)) { ?>
								<div class="phraseBox" style="margin:8px 8px 0 0;">FREE SHIPPING</div>
								<?php } ?>
								
							</div>
							
							
							<div class="Search-Avatar-Circle" style="z-index:1000;float:right;margin-top:-35px;background-image: url('<?php echo (($listing->relationships->vendor->data->avatarHashes->small!="")) ? "https://gateway.ob1.io/ob/images/".$listing->relationships->vendor->data->avatarHashes->small : asset_url()."img/defaultAvatar.png"?>');" title="<?=$listing->relationships->vendor->data->name?>" onclick="location.href='/store/<?=$listing->relationships->vendor->data->peerID?>;this.stopPropagation();'"></div>
							
							<div class="Discover-Body-Listing-Box-Desc">
								<div class="Discover-Body-Listing-Box-Title"><a href="/store/<?=$listing->relationships->vendor->data->peerID?>/<?=$listing->data->slug?>"><?=$listing->data->title?></a></div>
							</div>
							<div class="Listing-Details">
								<div class="Listing-Star">⭐</div>
								<div class="Listing-Rating"><?=$listing->data->averageRating?> (<?=$listing->data->ratingCount?>)</div>
								<div class="Listing-Price"><?=pretty_price($listing->data->price->amount, $listing->data->price->currencyCode);?></div>
							</div>
						</div>												
					
					<?php $i++; } ?>
					</div>
					
					<div style="text-align:center;width:100%;margin:0 auto;">
						<div style="border-radius: 2px;display: inline-block; box-shadow: 0 1px 0 0 rgba(219, 219, 219, 0.5);  background-color: #ffffff;  border: solid 1px #d2d3d9;margin:0 auto;margin-top:12px;padding:8px 33px;font-size:13px;font-weight:bolder;cursor:pointer" onclick="location.href='/discover/results/<?=$category?>';">See All</div>
					</div>
					
				
				
				
				
				
				<?php } ?>
				
				<br clear="both"/>	
				
			</div>
			
			
			
		</div>
		

		
	