<html lang="en" >
  <head>
        <meta charset="UTF-8">
        <title>bestreads</title>

        <!-- Custom CSS -->
  
        <style>

            body {
                font-family: "Helvetica Neue", Helvetica, Arial;
                font-size: 14px;
                line-height: 20px;
                font-weight: 400;
                color: #3b3b3b;
                -webkit-font-smoothing: antialiased;
                font-smoothing: antialiased;
                background: #87CEFA;
            }

            @media screen and (max-width: 580px) {
                body {
                font-size: 16px;
                line-height: 22px;
                }
            }

            .wrapper {
                float:left;
                width:60%;
                padding:0 20px;
            }

            .table {
                margin: 0 0 40px 0;
                width: 100%;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
                display: table;
            }

            @media screen and (max-width: 580px) {
                .table {
                display: block;
                }
            } 

            .row {
                display: table-row;
                background: #f6f6f6;
            }

            .row:nth-of-type(odd) {
                background: #e9e9e9;
            }
        
            .row.header {
                font-weight: 900;
                color: #ffffff;
                background: #ea6153;
            }

            .row.blue {
                background: #2980b9;
            }

            @media screen and (max-width: 580px) {
                .row {
                    padding: 14px 0 7px;
                    display: block;
                }
                .row.header {
                    padding: 0;
                    height: 6px;
                }
                .row.header .cell {
                    display: none;
                }
                .row .cell {
                    margin-bottom: 10px;
                }
                .row .cell:before {
                    margin-bottom: 3px;
                    content: attr(data-title);
                    min-width: 98px;
                    font-size: 10px;
                    line-height: 10px;
                    font-weight: bold;
                    text-transform: uppercase;
                    color: #969696;
                    display: block;
                }
            }

            .cell {
                padding: 6px 12px;
                display: table-cell;
            }

            @media screen and (max-width: 580px) {
                .cell {
                    padding: 2px 16px;
                    display: block;
                }
            }
        
            .category {
                float:center;
                background-color:#E6E6FA;
                padding:10px;
            }

            .bestseller{
                float:left;
                width:20%;
            }

        </style>

        <!-- End Custom CSS -->
    
    </head>

    <body>

        <?php 
            include ('./config.php'); 
            $book_length=0;
            $display_page=0;
            $total_page=0;
            $prev_page=0;
            $start=0;
            $book_catg_name="";
            $book_cover="";
            $book_title="";
            $book_author="";

            // Check the form after submission - Start

            if  ( (isset ($_POST['category_list']) || isset ($_POST['forward'] ) ) && (strcmp ($_POST['category_list'],"###")!=0) ) {
                $book_catg_name=$_POST['category_list'] ;
                $prev_book_catg_name=$_POST['prev_book_catg_name'] ;
                $prev_page=0;
                
                if  (isset ($_POST['page_count']) && isset ($_POST['forward']) && strcmp($prev_book_catg_name,$book_catg_name)==0 ) {
                    $prev_page=$_POST['page_count'];
                }

                // Feteching book (best-sellers) details from New_York_Times API - Start

                $curl = curl_init();
                curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
                $query = array(
                    "api-key" => $apiKey,
                    "list" => $book_catg_name
                );
                curl_setopt ($curl, CURLOPT_URL, "https://api.nytimes.com/svc/books/v3/lists.json" . "?" . http_build_query($query) );
                $b_result = json_decode(curl_exec($curl),true);
                curl_close($curl);  

                // Feteching  book (best-sellers) details from New_York_Times API - End


                // Sorting book list in descending order - Start

                $temp=count($b_result['results']);
                usort ($b_result['results'], function($a, $b) { //Sort the array using a user defined function
                    return $a['rank'] > $b['rank'] ? -1 : 1; //Compare the rank
                } ); 

                // Sorting book list in descending order - End


                // Feteching Book Cover from GOOGLE_API - Start
        
                if (!empty ($b_result['results'][$temp-1]['isbns']) ) {
                    $curl = curl_init();
                    curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt ($curl, CURLOPT_URL, "https://www.googleapis.com/books/v1/volumes?q=isbn:"  . $b_result['results'][$temp-1]['isbns'][0]['isbn10']);
                    $tmp= json_decode (curl_exec ($curl),true);
                    curl_close ($curl);
                    //   echo '<pre>'; print_r($b_result); echo '</pre>';
                    //  echo '<pre>'; print_r($tmp); echo '</pre>';
                    if(($tmp['totalItems']>0)&&(!empty($tmp['items'][0]['volumeInfo']['imageLinks']))) {
                        $book_cover=$tmp['items'][0]['volumeInfo']['imageLinks']['smallThumbnail'];
                    }
                }

                // Feteching Book Cover from GOOGLE_API - End

            
                // Feteching book title and author name of #RANK_1 book. - Start

                $book_title=$b_result['results'][$temp-1]['book_details'][0]['title'];
                $book_author=$b_result['results'][$temp-1]['book_details'][0]['author'];

                // Feteching book title and author name of #RANK_1 book. - End


                // Controling page according to page number - Start

                $total_page=ceil($temp/10);

                if($prev_page>=$total_page) {
                    $prev_page=$prev_page-1;
                }

                $start=$prev_page*10;

                if(($temp-$start)>=10)  {
                    $book_length=$start+10;
                }
                else {
                    $book_length=$temp;
                }

                // Controling Page according to page number - End

                $display_page=$prev_page+1;

            }

            // Check the form after submission - End


            // Fetching book genres from New_York_Times API - Start

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $query = array("api-key" => "65c3ea1815734923abc1b5ab073e9032");
            curl_setopt($curl, CURLOPT_URL,"https://api.nytimes.com/svc/books/v3/lists/names.json" . "?" . http_build_query($query));
            $result = json_decode(curl_exec($curl),true);
            curl_close($curl);

            // Fetching book genres from New_York_Times API - End

            $catg_length=$result['num_results'];

        ?>

        <!-- Displaying select options for book genres - Start -->

        <div class="category ">
            <form method="post" action="">
                <div>
                    <select name="category_list">
                        <option value="###">Select Genres</option>
                
                        <?php

                            // Sorting the book_genres in alphabatecial orders - Start

                            usort($result['results'], function($a, $b) { //Sort the array using a user defined function
                                return strcmp($a['display_name'] , $b['display_name'])>0 ? 1 : -1; //Compare the display_name
                            });

                            // Sorting the book_genres in alphabatecial orders - End

                            for($i=0;$i<$catg_length;$i++)  {
                                $list_name=$result['results'][$i]['list_name'];
                                $display_name=$result['results'][$i]['display_name'];

                            ?>

                            <option value="<?php echo $list_name; ?>" <?php if(strcmp($book_catg_name,$list_name)==0 ){echo " selected"; }?>>
                                <?php 
                                    echo $display_name; 
                                ?>
                            </option>

                            <?php
                        }

                        ?>

                    </select>
                    <input type="hidden" name="prev_book_catg_name" value="<?php echo $book_catg_name;?>" />
                    <input type="submit" value="Show Books">

                </div>

                <div style="float:right;">
                    <label for="page">Page <?php echo $display_page;?> of <?php echo $total_page;?></label>
                    <input type="hidden" name="page_count" value="<?php echo $display_page;?>" />
                    <input type="submit" name="forward" value="next">
                </div>

            </form>
            </br>
        </div>
        </br>

        <!-- Displaying select options for book genres - End  -->


        <!-- Publishing the BOOK COVER of #RANK_1 BESTSELLER - Start -->

    
        <?php

            if($book_length!=0) {

        ?>

            <div class="bestseller">
                <h3>#Rank_1 bestseller in this genre is :-  </h3>

                <?php

                    if(!empty($book_cover)) {

                ?>

                        <img src=<?php echo $book_cover; ?> alt="No Cover Found">

                        <?php

                    }
                    else {
                        echo ' <strong>"</strong>Sorry <strong>:(</strong> The book cover is not available.<strong>"</strong> ';
                    }

                    ?>
                    <p><strong><?php echo $book_title; ?></strong> </p>
                    <p>By</p>
                    <p><strong><?php echo $book_author; ?></strong></p>
                    </br>

            </div>
            <?php

        }
      
        ?>
 

        <!-- Publishing the BOOK COVER of #RANK_1 BESTSELLER -End  -->

        <!-- Showing the bestseller books of selected genre - Start -->

        <div class="wrapper">
            <div class="table">
                <div class="row header blue">

                    <div class="cell">
                        Rank
                    </div>

                    <div class="cell">
                        Title
                    </div>

                    <div class="cell">
                        Author
                    </div>

                    <div class="cell">
                        Discription
                    </div>

                </div>

                <?php

                    if($book_length!=0) {

                        for($i=$start;$i<$book_length;$i++) {
                            $rank=$b_result['results'][$i]['rank'];
                            $title=$b_result['results'][$i]['book_details'][0]['title'];
                            $author=$b_result['results'][$i]['book_details'][0]['author'];
                            $description=$b_result['results'][$i]['book_details'][0]['description'];

                            ?>

                            <div class="row">

                                <div class="cell" data-title="Rank">

                                    <?php 
                                        echo $rank; 
                                    ?>

                                </div>

                                <div class="cell" data-title="Title">

                                    <?php

                                        if(empty($title)) {
                                            echo "No Title Available";
                                        }
                                        else  {
                                            echo $title; 
                                        }

                                    ?>

                                </div>

                                <div class="cell" data-title="Author">

                                    <?php

                                        if(empty($author))  {
                                            echo "No Author Available";
                                        }
                                        else  {
                                            echo $author; 
                                        }

                                    ?>

                                </div>

                                <div class="cell" data-title="Discription">

                                <?php 

                                    if(empty($description)) {
                                    echo "No Description Available";
                                    }
                                    else  {
                                    echo $description;
                                    }      

                                ?>

                            </div>

                        </div>

                        <?php

                    } 

                }
    
                ?>    
    
            </div>
  
        </div>

        <!-- End of Showing the bestseller books of selected genre -->  

    </body>

</html>
