<div class="st-destination_tab">
    <?php
        $array_parent = [
            'post_type' => 'location',
            'post_parent' => 0,
            'nopaging' => true,
        ];
        $count = 0;
        $query_location = new WP_Query($array_parent);
        if($query_location->have_posts()){?>
            <ul class="st-destination_tab_list nav nav-tabs justify-content-center" role="tablist">
                <?php while($query_location->have_posts()) : $query_location->the_post();?>
                <li class="st-destination_tab_nav nav-item" role="presentation">
                    <a class="nav-link <?php echo ($count == 0)? 'active' : '';?>" id="<?php echo st_convert_characers_to_slug(get_the_title());?>_tab" data-bs-toggle="tab" href="#<?php echo st_convert_characers_to_slug(get_the_title());?>" role="tab" aria-controls="<?php echo st_convert_characers_to_slug(get_the_title());?>" aria-selected="<?php echo ($count == 0)? 'true' : '';?>">
                    <?php echo get_the_title();?>
                    </a>
                </li>
                <?php
                $count ++;
                endwhile;?>
            </ul>
                
            <?php
            wp_reset_postdata();    
        }

        $item_destination_parent = get_posts($array_parent);
        ?>
    <div class="tab-content">
        <?php 
            $count_tab = 0;
            foreach($item_destination_parent as $destination_parent): ?>
                <div class="tab-pane fade <?php echo ($count_tab == 0) ? 'show active' : '';?>" id="<?php echo st_convert_characers_to_slug(get_the_title($destination_parent->ID));?>" role="tabpanel" aria-labelledby="<?php echo st_convert_characers_to_slug(get_the_title($destination_parent->ID));?>">
                    <div class="row st-list-destination">
                        <?php 
                            $descendants = tth_get_posts_grandchildren($destination_parent->ID , 'location');
                            if(!empty($descendants)){
                                $args_by_parent = [
                                    'post_type' => 'location',
                                    'post__in' => tth_get_IDs_by_list_post_object($descendants),
                                ];
                               
                                
                                $child_location = new WP_Query($args_by_parent);
                                if($child_location->have_posts()){
                                    while($child_location->have_posts()) : $child_location->the_post();
                                        $class = 'col-12 col-sm-6 col-md-6 col-lg-4 col-xl-4 normal-item';
                                        if($number_show_in_row == '2'){
                                            $class = 'col-12 col-sm-6 col-md-6 normal-item';
                                        } elseif ($number_show_in_row == '4'){
                                            $class = 'col-12 col-sm-6 col-md-6 col-lg-3 col-xl-3 normal-item';
                                        }elseif ($number_show_in_row == '3'){
                                            $class = 'col-12 col-sm-6 col-md-6 col-lg-4 col-xl-4 normal-item';
                                        } else {
                                            $class = 'col-12 normal-item';
                                        }
                                        ?>
                                        <div class="<?php echo esc_attr($class); ?>">
                                            <div class="destination-item">
                                                <div class="image st-border-radius">
                                                    <a class="st-link" href="<?php echo esc_url(get_the_permalink()) ?>">
                                                        <?php
                                                            echo get_the_post_thumbnail( get_the_ID(), 'full', array( 'class' => 'img-fluid', 'alt' => get_the_title() ) ); 
                                                        ?>
                                                    </a>
                                                    <div class="content">
                                                        <h3 class="title">
                                                            <a href="<?php echo esc_url(get_the_permalink()); ?>">
                                                                <?php the_title() ?>
                                                            </a>
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile;
                                    wp_reset_postdata();
                                }
                            }
                            ?>
                    </div>
                </div>
            <?php 
            $count_tab++;
            endforeach;
        ?>
    </div>
</div>
