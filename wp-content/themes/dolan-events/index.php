<?php get_header(); ?>
	
		<div id="left-content">
            
           <div id="event">
            		<object width="400" height="225" data="https://vimeo.com/moogaloop.swf?clip_id=10435535&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=&amp;fullscreen=1&amp;autoplay=0&amp;loop=0" type="application/x-shockwave-flash">
						<param value="best" name="quality">
						<param value="true" name="allowfullscreen">
						<param value="showAll" name="scale">
						<param name="movie" value="https://vimeo.com/moogaloop.swf?clip_id=10435535&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=&amp;fullscreen=1&amp;autoplay=0&amp;loop=0" />
						<param value="opaque" name="wmode">
					</object>
            		<div id="registration">
                	<h2>Very Important Professionals</h2>
                    <p>Thursday, October 21 2010<br />American Visionary Art Museum</p>
                    <blockquote>"Innovators are people with vision. With the ability to see a need and fill it. With the courage to make change and the stamina to await the results."</blockquote>
                    <a class="register" href="">Register Now</a>
                	</div>
                </div>
            	<p class="title"><span>Innovator of the Year</span> was created in 2002 to honor Maryland businesses and/or individuals who have had a positive effect and tremendous impact in Maryland.</p>
                <div id="btm-buttons">
                <a class="button" href=""><span class="submit">Submit An Application</span></a>
                <a class="button" href=""><span class="nominate">Nominate Someone</span></a>
                <a class="button" href=""><span class="invite">Request An Invite</span></a>			
                </div>
                <div id="past-event">
                	<div id="col1">
                        <h1>Winners</h1>
                        <p>View past winners by selecting a year below.</p>
                        <form name="selectYear" id="selectYear" action="">
                        	<select name="year">
                            <option value="selectYear">Select a Year</option>
                            <option value="Year1">Year1</option>
                            <option value="Year2">Year2</option>
                            <option value="Year3">Year3</option>
                            <option value="Year4">Year4</option>
                            </select>
                            <input name="goBtn" class="goBtn" value="Go" type="image" src="<?php bloginfo('stylesheet_directory'); ?>/images/go-btn.png" />
                        </form>
                        <p class="photos">Need photos from a past event?</p>
                        <img class="reprint-photo" src="<?php bloginfo('stylesheet_directory'); ?>/images/past-photos.jpg" alt="" />
                        <a class="reprints" href="">Order Reprints</a>
                    </div>
                    <div id="col2">
                    	<h1>Event Photos</h1>
                        <div id="photos">
                          <img src="<?php bloginfo('stylesheet_directory'); ?>/images/photo-1.jpg" alt="1" width="277" height="188" />
                          <img src="<?php bloginfo('stylesheet_directory'); ?>/images/photo-2.jpg" alt="2" width="277" height="188" />
                          <img src="<?php bloginfo('stylesheet_directory'); ?>/images/photo-3.jpg" alt="3" width="277" height="188" />
                          <img src="<?php bloginfo('stylesheet_directory'); ?>/images/photo-4.jpg" alt="4" width="277" height="188" />
                          <img src="<?php bloginfo('stylesheet_directory'); ?>/images/photo-5.jpg" alt="3" width="277" height="188" />
                          <img src="<?php bloginfo('stylesheet_directory'); ?>/images/photo-6.jpg" alt="4" width="277" height="188" />
                        </div>
                        <ul id="controls">
                          <li><a href="#prev" id="prev">Previous</a></li>
                          <li><a href="#next" id="next">Next</a></li>
                        </ul>
                    </div>
                </div>
            </div>
			
			<?php get_sidebar(); ?>
            
<?php get_footer(); ?>