<?php

function tie_breadcrumbs() {
  // Check if we're showing breadcrumbs & if it isn't home or front paged and is paged
  if(tie_get_option('breadcrumbs')){
    $delimiter = tie_get_option('breadcrumbs_delimiter') ? tie_get_option('breadcrumbs_delimiter') : '&raquo;';
    global $author, $post, $wp_query;

    if ( !is_home() && !is_front_page() || is_paged() ) {

    // Regex hack to clean up double-delimiters
    $patterns = array( '/\|tie\|\Z/', '/\|tie\|/' );
    $replace = array( '', ' ' . $delimiter . ' ' );
    $breadcrumbs = array();

    // Non-filterable breadcrumb wrapper
    $output = '<div xmlns:v="http://rdf.data-vocabulary.org/#"  id="crumbs">';

    // Build the 'Home' portion of the breadcrumb
    $cur_blog_id = defined( 'BLOG_ID_CURRENT_SITE' )?  BLOG_ID_CURRENT_SITE :  1;
    $homeLink = get_site_url( $cur_blog_id );
    $breadcrumbs[0] = '<span typeof="v:Breadcrumb"><a rel="v:url" property="v:title" class="crumbs-home" href="' . $homeLink . '"><i class="tieicon-home"></i>' . __( 'Home' , 'tie' ) . '</a></span>';
    if ( is_category() ) {
      // Category breadcrumb
      $cat_obj = $wp_query->get_queried_object();
      $thisCat = $cat_obj->term_id;
      $thisCat = get_category( $thisCat );
      $parentCat = get_category( $thisCat->parent );
      if ( $thisCat->parent != 0 ) {
        // For child categories, build list of parents
        if( !is_wp_error( $cat_code = get_category_parents($parentCat, TRUE, '|tie|' ) ) ) {
          $cat_code = str_replace('<a', '<span typeof="v:Breadcrumb"><a rel="v:url" property="v:title"', $cat_code);
          $cat_code = str_replace('</a>', '</a></span>', $cat_code);
          $breadcrumbs[1] = preg_replace( $patterns, $replace, $cat_code );

        }
      }
      $breadcrumbs[2] =  single_cat_title( '', false );
    }
    elseif ( is_tax() ) {
      // Taxonomy breadcrumb
      $term = $wp_query->get_queried_object();
      $taxtitle = $term->name;
      $breadcrumbs[1] =  $taxtitle;
    }
    elseif ( is_day() ) {
      // Day archive breadcrumb
      $breadcrumbs[1] = '<span typeof="v:Breadcrumb"><a rel="v:url" property="v:title" href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a></span>';
      $breadcrumbs[2] = '<span typeof="v:Breadcrumb"><a rel="v:url" property="v:title" href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a></span>';
      $breadcrumbs[3] = get_the_time('d');
    }
    elseif ( is_month() ) {
      // Month archive breadcrumb
      $breadcrumbs[1] = '<span typeof="v:Breadcrumb"><a rel="v:url" property="v:title" href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a></span>';
      $breadcrumbs[2] = get_the_time('F');
    }
    elseif ( is_year() ) {
      // Year archive breadcrumb
      $breadcrumbs[1] = get_the_time('Y');
    }
    elseif ( is_single() && !is_attachment() ) {
      // Post breadcrumbs
      if ( get_post_type() != 'post' ) {
        // Custom post type
        $post_type = get_post_type_object(get_post_type());
        $slug = $post_type->rewrite;
        $breadcrumbs[1] = '<span typeof="v:Breadcrumb"><a rel="v:url" property="v:title" href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a></span>';
        $breadcrumbs[2] = get_the_title();
      }
      else {
        // WordPress post
        $cat = get_the_category();
        $cat = $cat[0];
        if ( !empty( $cat ) ) {
          $cat_code = get_category_parents( $cat, TRUE, '|tie|' );
          if( !is_wp_error( $cat_code = get_category_parents($cat, TRUE, '|tie|' ) ) ) {
            $cat_code = str_replace('<a', '<span typeof="v:Breadcrumb"><a rel="v:url" property="v:title"', $cat_code);
            $cat_code = str_replace('</a>', '</a></span>', $cat_code);
            $breadcrumbs[1] = preg_replace($patterns, $replace, $cat_code);
          }
        }
        $breadcrumbs[2] = get_the_title();
      }
    }

  }elseif ( (is_page() && !$post->post_parent) || ( function_exists('bp_current_component') && bp_current_component() ) ) {
    $breadcrumbs[1] = get_the_title();
  }elseif ( !is_single() && !is_page() && get_post_type() != 'post' ) {
      $post_type = get_post_type_object(get_post_type());
      $breadcrumbs[1] = $post_type->labels->singular_name;
    }
		elseif ( is_attachment() ) {
      // Attachment breadcrumb
      $parent = get_post( $post->post_parent );
      $breadcrumbs[1] = '<span typeof="v:Breadcrumb"><a rel="v:url" property="v:title" href="' . get_permalink($parent) . '">' . $parent->post_title . '</a></span>';
      //$breadcrumbs[2] = get_the_title();
    }
		elseif ( is_page() && !$post->post_parent ) {
      // Page with no parent breadcrumb
      $breadcrumbs[1] =  get_the_title();
    }
		elseif ( is_page() && $post->post_parent ) {
      // Child page breadcrumb
      $ancestors = get_post_ancestors( $post );
      foreach ( array_reverse( $ancestors ) as $ancestor ) {
        $breadcrumbs[] = '<span typeof="v:Breadcrumb"><a rel="v:url" property="v:title" href="' . get_permalink($ancestor) . '">' . get_the_title($ancestor) . '</a></span>';

      }
      $breadcrumbs[] = get_the_title();
    }
		elseif ( is_search() ) {
      // Search page breadcrumb
      $breadcrumbs[1] = sprintf( __( 'Search Results for: %s', 'tie' ),  get_search_query() );
    }
		elseif ( is_tag() ) {
      // Tag archive breadcrumb
      $breadcrumbs[1] = sprintf( __( 'Tag Archives: %s', 'tie' ), single_tag_title( '', false ) );
    }
		elseif ( is_author() ) {
      // Author archive breadcrumb
      $userdata = get_userdata( $author );
      $breadcrumbs[1] = sprintf( __( 'Author Archives: %s', 'tie' ),  $userdata->display_name );
    }
		elseif ( is_404() ) {
      // 404 breadcrumb
      $breadcrumbs[1] = __( 'Not Found', 'tie' );
    }

		// Append page number to paged archives
		if ( get_query_var( 'paged' ) && ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) ) {
          $breadcrumbs[count( $breadcrumbs ) - 1] .= ' (' . __('page ' , 'tie') . ' ' . get_query_var( 'paged' ) . ')';
        }

		// Filter & output breadcrumb
		$breadcrumbs = apply_filters( 'tie_breadcrumbs', $breadcrumbs );
		foreach( $breadcrumbs as $index => $crumb ) {
          if( $index !== count( $breadcrumbs ) - 1 ) {
            $output .= $crumb . ' ' . $delimiter . ' ';
          }
          else {
            $output .= '<span class="current">' . $crumb . '</span>';
          }
        }
		echo $output . '</div>';
	}
}
?>