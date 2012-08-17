<?php
    /*
        class.blog.php
        
        This is our blog class that handles the blog posts on the site.
    */
    
    // begin our class
    class GBlog {
        // set our base vars for our blog
        public $bID       = NULL;
        public $bTitle    = "";
        public $bText     = "";
        public $bOwner    = 0;
        public $bPostDate = "";
        public $bActive   = 1;
        public $tags      = "";
        
        // create our comment vars
        public $cBlog     = 0;
        public $cID       = 0;
        public $cTitle    = "";
        public $cText     = "";
        public $cOwner    = 0;
        public $cPostDate = "";
        
        // set our error text
        public $errorText  = "";
        
        // set our table names
        private $dbConn;
        private $parser;
        private $tableBlogs       = "blogs";
        private $tableComments    = "blog_comments";
        private $tableTags        = "blog_tags";
        private $tableMembers     = "members";
        private $tableMembersID   = "members.ID";
        private $tableMembersName = "members.strUsername";
        private $titleLength      = "250";
        private $teaserLength     = "45";
        private $dateFormat       = "M. jS, Y \@ g:i a";      // use the PHP date format as you wish
        
        /**
        * Constructor. Checks if the file has been uploaded
        *
        * The constructor takes the PEAR database connection and creates our local object so
        * we can use it within the class.
        *
        * @access private
        * @param  object $dbConn PEAR::DB object
        * 
        */
        function __construct($dbConn, $parser = "") {
            // set our db connection as passed (currently PEAR::DB)
            $this->dbConn = $dbConn;
            $this->parser = $parser;
        }
        
        /**
        * addBlogPost()
        *
        * This allows us to add a new blog post into the db.
        *
        * @access public
        * @param  text    $title  The title of the post to be added
        * @param  text    $post   The content of the actual blog post
        * @param  date    $date   The date to add as the post date into the database.
        * @param  int     $owner  The user ID who has posted the blog.
        * @param  boolean $active Whether the post is active or not
        * @param  text    $tags   A string of tags listed for this post.
        * 
        */
        function addBlogPost($title, $post, $date, $owner, $active = 1, $tags = "") {
            // set our content
            $this->bTitle      = $this->validateBlogTitle($title);
            $this->bText       = $this->validateBlogText($post);
            $this->bPostDate   = $date;
            $this->bOwner      = $owner;
            $this->bActive     = $active;
            
            // see if a comma separated list was passed
            if (!empty($tags)) {
                $this->getUniqueTags($tags);
            }
            
            // save the data into the db
            $this->runBlogUpdate('add');
        }
        
        /**
        * updateBlogPost()
        *
        * Our function to update the vars for this specific post
        *
        * @access public
        * @param  int     $id     The ID of the blog post to update
        * @param  text    $title  The title of the post to be added
        * @param  text    $post   The content of the actual blog post
        * @param  int     $owner  The user ID who has posted the blog.
        * @param  boolean $active Whether the post is active or not
        * @param  text    $tags   A string of tags listed for this post.
        * 
        */
        function updateBlogPost($id, $title, $post, $owner, $user, $active, $tags = "") {
            // make sure this user can edit this info
            $this->validateBlogOwner($user, $owner);
            
            // set our content
            $this->bID         = $id;
            $this->bTitle      = $this->validateBlogTitle($title);
            $this->bText       = $this->validateBlogText($post);
            $this->bOwner      = $owner;
            $this->bActive     = $active;
            
            // see if a comma separated list was passed
            if (!empty($tags)) {
                $this->getUniqueTags($tags);
            }
            
            // save the data into the db
            $this->runBlogUpdate('update');
        }
        
        /**
        * deleteBlogPost()
        *
        * Our function to update the vars for this specific post
        *
        * @access public
        * @param  int     $id     The ID of the blog post to delete
        * @param  int     $owner  The user ID who has posted the blog
        * @param  int     $user   The ID of the user trying to delete the post
        * 
        */
        function deleteBlogPost($id, $owner, $user) {
            // make sure this user can edit this info
            $this->validateBlogOwner($user, $owner);
            
            // set our content
            $this->bID         = $id;
            $this->bOwner      = $owner;
            
            // save the data into the db
            $this->runBlogUpdate('delete');
        }
        
        /**
        * runBlogUpdate()
        *
        * Our function to run our update into the db
        *
        * @access private
        * @param  txt     $command   What we need to do (add, update, or delete)
        * 
        */
        function runBlogUpdate($command) {
            // see if we need to stop
            $this->checkBlogError();
            
            // based on our passed command, continue
            switch ($command) {
                case "add":
                    // add the data into the db
                    $qryAdd = $this->dbConn->query("INSERT INTO `" . $this->tableBlogs . "` ( title, blogpost, uid, postdate, active ) VALUES ( '" . $this->bTitle . "', '" . $this->bText . "', '" . $this->bOwner . "', '" . $this->bPostDate . "', '" . $this->bActive . "' )");
                    
                    // if it was added, we're all good
                    if (mysql_insert_id($this->dbConn->connection)) {
                        // set the blog ID
                        $this->bID = mysql_insert_id($this->dbConn->connection);
                    } else {
                        // set the error message
                        $this->errorText = "The blog post could not be added.  Please try again";
                        $this->checkBlogError();
                    }
                    
                    // add our tags
                    $this->addBlogTags();
                    
                    break;
                
                case "update":
                    // update the db with the new passed data
                    $qryUpdate = $this->dbConn->query("UPDATE `" . $this->tableBlogs . "` SET title = '" . $this->bTitle . "', blogpost = '" . $this->bText . "', active = '" . $this->bActive . "' WHERE id = '" . $this->bID . "' AND uid = '" . $this->bOwner . "' LIMIT 1");
                    
                    // add our tags
                    $this->updateBlogTags();
                    
                    break;
                
                case "delete":
                    // update the db with the new passed data
                    $qryUpdate = $this->dbConn->query("DELETE FROM `" . $this->tableBlogs . "` WHERE id = '" . $this->bID . "' AND uid = '" . $this->bOwner . "' LIMIT 1");
                    
                    // see if it was added
                    if (!$this->dbConn->affectedRows()) {
                        // set the error message
                        $this->errorText = "The blog post was not updated successfully.  Error: " . $this->dbConn->getMessage();
                        $this->checkBlogError();
                    }
                    
                    // remove our tags
                    $this->deleteBlogTags();
                    
                    // remove comments for this post
                    $this->deleteAllBlogComments();
                    
                    break;
            }
        }
        
        /**
        * FUNCTIONS TO HANDLE THE TAGS FOR THE POSTS
        */
        
        /**
        * addBlogTags()
        *
        * Our function to add tags for new blog posts
        *
        * @access private
        * 
        */
        function addBlogTags() {
            // if we have an array of tags, add each individually
            if (!empty($this->tags) && count($this->tags)) {
                // loop through the array and add them to the table
                foreach ($this->tags as $tag) {
                    // add it to the db
                    $qryAdd = $this->dbConn->query("INSERT INTO `" . $this->tableTags . "` ( bid, tag ) VALUES ( '" . $this->bID . "', '" . trim($tag) . "' )");
                }
            }
        }
        
        /**
        * FUNCTIONS TO HANDLE THE TAGS FOR THE POSTS
        */
        
        /**
        * updateBlogTags()
        *
        * Our function to change our tags when a post has been changed
        *
        * @access private
        * 
        */
        function updateBlogTags() {
            // delete all current tags from the db
            $this->deleteBlogTags();
            
            // if we have an array of tags, add each individually
            $this->addBlogTags();
        }
        
        /**
        * FUNCTIONS TO HANDLE THE TAGS FOR THE POSTS
        */
        
        /**
        * deleteBlogTags()
        *
        * Our function to change our tags when a post has been changed
        *
        * @access private
        * 
        */
        function deleteBlogTags() {
            // delete all current tags from the db
            $qryDelete = $this->dbConn->query("DELETE FROM `" . $this->tableTags . "` WHERE bid = '" . $this->bID . "'");
        }
        
        /**
        * FUNCTIONS TO HANDLE THE TAGS FOR THE POSTS
        */
        
        /**
        * getUniqueTags()
        *
        * Our function to create our unique tags
        *
        * @access private
        * @param  text  $tags  A comma-separated list of tags
        * 
        */
        function getUniqueTags($tags) {
            // create an array from the list
            $arrTags = array();
            
            // loop through and trim the tags
            foreach (explode(",", $tags) as $tag) {
                $arrTags[] = trim(strip_tags($tag));
            }
            
            // set our tags value with unique values
            $this->tags = array_unique($arrTags);
        }
        
        /**
        * DISPLAY FUNCTIONS
        */
        
        /**
        * displayBlogPosts()
        *
        * Our function to display our blog values
        *
        * @access private
        * @param  int   $user       An ID of a user to display
        * @param  text  $tag        A tag to search the database for
        * @param  text  $search     A search phrase to match posts against
        * 
        */
        function displayBlogPosts($user = 0, $tag = "", $search = "") {
            // create our array from our data
            if (!empty($search)) {
                $arrPosts = $this->searchPosts($search);
            } else if (!empty($tag)) {
                $arrPosts = $this->getTaggedPosts($tag);
            } else {
                $arrPosts = $this->getUserPosts($user);
            }
            
            // see if we have any data to display
            if (count($arrPosts)) {
                // loop through our array and display the content
                for ($i = 0; $i < count($arrPosts); $i++) {
                    ?>
                    <div class="blog_post">
                        <div class="blog_title"><h2><a href="/blogs/view.php?id=<?php print $arrPosts[$i]["id"]; ?>" title="<?php print $arrPosts[$i]["title"]; ?>"><?php print $arrPosts[$i]["title"]; ?></a></h2></div>
                        <div class="blog_owner">Posted by <a href="/blogs/index.php?id=<?php print $arrPosts[$i]["uid"]; ?>" title="Posts by <?php print $arrPosts[$i]["username"]; ?>"><?php print $arrPosts[$i]["username"]; ?></a> on <?php print $arrPosts[$i]["postdate"]; ?></div>
                        <div class="blog_teaser"><?php $this->displayBlogTeaser($arrPosts[$i]["blogpost"]); ?></div>
                        <div class="blog_read"><a href="view.php?id=<?php print $arrPosts[$i]["id"]; ?>" title="Read the entire post">Read Full Post</a> | <a href="/blogs/view.php?id=<?php print $arrPosts[$i]["id"]; ?>#comment" title="Comment on this post">Comments</a> (<?php print $arrPosts[$i]["comments"]; ?>)</div>
                        <div class="blog_tags">Tagged as: <?php $this->displayTagLinks($arrPosts[$i]["tags"]); ?></div>
                    </div>
                    <?php
                }
                
            } else {
                // nothing to display
                ?>
                <div class="blog_post">There are currently <b>0</b> blog posts in the database that match your request.</div>
                <?php
            }
        }
        
        // our function to display the html for a chosen array of posts
        function displayBlogPostHTML($post, $user = 0) {
            // display our HTML
            ?>
            <div class="blog_post_full">
                <div class="blog_owner">Posted by <a href="/blogs/index.php?id=<?php print $post["uid"]; ?>" title="Posts by <?php print $post["username"]; ?>"><?php print $post["username"]; ?></a> on <?php print $post["postdate"]; ?>
            <?php
                // if this is the owner, allow them to edit their post
                if ($user && $post["uid"] == $user) {
                    ?> | 
                    <a href="/blogs/post.php?id=<?php print $post["id"]; ?>&action=edit" title="Edit Your Post" class="blog_edit">Edit</a> | 
                    <a href="/blogs/post.php?id=<?php print $post["id"]; ?>&action=delete" onclick="return confirm('Are you sure you want to remove this post?  It, and all comments, will be permanently removed.  This cannot be undone.');" title="Delete Your Post" class="blog_delete">Delete</a>
                    <?php
                }
            ?></div>
                <div class="blog_teaser"><?php print $this->convertLineBreaks($post["blogpost"]); ?></div>
                <div class="blog_tags">Tagged as: <?php $this->displayTagLinks($post["tags"]); ?></div>
            </div>
            <?php
        }
        
        // our function to display our blog values
        function displayFullBlogPost($id) {
            // create our array from our data
            return $this->getBlogPostArray($id);
        }
        
        // our function to display posts, based on user selection (if any)
        function getUserPosts($user) {
            // set the where clause, based on the user
            if (!empty($user)) {
                $where = $this->tableBlogs . ".uid = '" . $user . "' AND ";
            } else {
                $where = "";
            }
            
            // query the db for data we need to display
            $qryBlogs = $this->dbConn->query("SELECT " . $this->tableBlogs . ".*, " . $this->tableMembersID . ", " . $this->tableMembersName . " as username, COUNT(" . $this->tableComments . ".id) as comments FROM (" . $this->tableBlogs . ", " . $this->tableMembers . ") LEFT JOIN " . $this->tableComments . " ON (" . $this->tableBlogs . ".id = " . $this->tableComments . ".bid) WHERE " . $where . " " . $this->tableBlogs . ".active = '1' AND " . $this->tableBlogs . ".uid = " . $this->tableMembersID . " GROUP BY " . $this->tableBlogs . ".postdate ORDER BY " . $this->tableBlogs . ".postdate DESC");
            
            // create an array to display
            return $this->createBlogPostArray($qryBlogs);
        }
        
        // our function to display posts, based on user selection (if any)
        function searchPosts($phrase) {
            // query the db for data we need to display
            $qryBlogs = $this->dbConn->query("SELECT " . $this->tableBlogs . ".*, " . $this->tableMembersID . ", " . $this->tableMembersName . " as username, COUNT(" . $this->tableComments . ".id) AS comments FROM (" . $this->tableBlogs . ", " . $this->tableMembers . ") LEFT JOIN " . $this->tableComments . " ON (" . $this->tableBlogs . ".id = " . $this->tableComments . ".bid)
WHERE ((" . $this->tableBlogs . ".title LIKE '%" . $phrase . "%' OR " . $this->tableBlogs . ".blogpost LIKE '%" . $phrase . "%') OR ((" . $this->tableComments . ".title LIKE '%" . $phrase . "%' OR " . $this->tableComments . ".comment LIKE '%" . $phrase . "%') AND " . $this->tableComments . ".bid = " . $this->tableBlogs . ".id)) AND " . $this->tableBlogs . ".active = '1' AND " . $this->tableBlogs . ".uid = " . $this->tableMembersID . " GROUP BY " . $this->tableBlogs . ".title");
            
            // create an array to display
            return $this->createBlogPostArray($qryBlogs);
        }
        
        // our function to display posts, based on a passed tag
        function getTaggedPosts($tag) {
            // query the tags table for all posts tagged with this tag
            $qryTags = $this->dbConn->query("SELECT bid FROM " . $this->tableTags . " WHERE tag = '" . $tag . "'");
            
            // create our array of post ID's from the tags table
            $arrPosts = $this->createPostTagArray($qryTags);
            
            // query the db for data we need to display
            $qryBlogs = $this->dbConn->query("SELECT " . $this->tableBlogs . ".*, " . $this->tableMembers . ".ID, " . $this->tableMembersName . " as username, COUNT(" . $this->tableComments . ".id) as comments FROM (" . $this->tableBlogs . ", " . $this->tableMembers . ") LEFT JOIN " . $this->tableComments . " ON (" . $this->tableBlogs . ".id = " . $this->tableComments . ".bid) WHERE " . $this->tableBlogs . ".id IN ( " . $arrPosts . " ) AND " . $this->tableBlogs . ".active = '1' AND " . $this->tableBlogs . ".uid = " . $this->tableMembers . ".ID GROUP BY " . $this->tableBlogs . ".postdate ORDER BY " . $this->tableBlogs . ".postdate DESC");
            
            // create an array to display
            return $this->createBlogPostArray($qryBlogs);
        }
        
        // our function to display posts, based on a passed tag
        function getBlogPostArray($id) {
            // query the db for data we need to display
            $qryPost = $this->dbConn->query("SELECT " . $this->tableBlogs . ".*, " . $this->tableMembers . ".ID, " . $this->tableMembersName . " as username, COUNT(" . $this->tableComments . ".id) as comments FROM (" . $this->tableBlogs . ", " . $this->tableMembers . ") LEFT JOIN " . $this->tableComments . " ON (" . $this->tableBlogs . ".id = " . $this->tableComments . ".bid) WHERE " . $this->tableBlogs . ".id = '" . $id . "' AND " . $this->tableBlogs . ".active = '1' AND " . $this->tableBlogs . ".uid = " . $this->tableMembers . ".ID GROUP BY " . $this->tableBlogs . ".postdate ORDER BY " . $this->tableBlogs . ".postdate DESC");
            
            // return our array
            return $this->createBlogPostArray($qryPost);
        }
        
        // our function to create our array of data to display
        function createBlogPostArray($qryBlogs) {
            // create our array of posts
            $arrResults = array();
            
            // if we have results, continue
            if ($qryBlogs->numRows()) {
                // loop through our results and add to our array
                while ($qryRow = $qryBlogs->fetchRow(DB_FETCHMODE_ASSOC)) {
                    // see if the BBCode parser has been set
                    if (method_exists($this->parser, "qParse")) {
                        $blogpost = trim($this->parser->qParse($qryRow["blogpost"]));
                    } else {
                        $blogpost = trim($qryRow["blogpost"]);
                    }
                    
                    // create our tags array
                    $postTags = $this->createBlogPostTagsArray($qryRow["id"]);
                    
                    // add all of the data to the array
                    $arrResults[] = array(
                                        "id"        => $qryRow["id"],
                                        "title"     => $qryRow["title"],
                                        "blogpost"  => $blogpost,
                                        "postdate"  => date($this->dateFormat, strtotime($qryRow["postdate"])),
                                        "uid"       => $qryRow["uid"],
                                        "active"    => $qryRow["active"],
                                        "comments"  => $qryRow["comments"],
                                        "tags"      => $postTags,
                                        "username"  => $qryRow["username"]
                                         );
                }
            }
            
            // return the full array
            return $arrResults;
        }
        
        // our function to create our array of data to display
        function createBlogPostTagsArray($id) {
            // create our temp array
            $postTags = array();
            
            // get any/all tags for this post
            $qryTags = $this->dbConn->query("SELECT tag FROM " . $this->tableTags . " WHERE bid = '" . $id . "' ORDER BY tag");
            
            // if we have records, add them to our array
            if ($qryTags->numRows()) {
                while ($qryRow = $qryTags->fetchRow(DB_FETCHMODE_ASSOC)) {
                    // add the tag to our array
                    $postTags[] = trim($qryRow["tag"]);
                }
            }
            
            // return the array of tags
            return $postTags;
        }
        
        // our function to create our IN string for our tagged query
        function createPostTagArray($qryTags) {
            // create our storage array
            $arrPostIDs = array();
            
            // loop through our resultset
            while ($qryRow = $qryTags->fetchRow(DB_FETCHMODE_ASSOC)) {
                $arrPostIDs[] = $qryRow["bid"];
            }
            
            // return our string
            return "'" . implode("','", $arrPostIDs) . "'";
        }
        
        // our function to display the tags links
        function displayTagLinks($tags) {
            // if we have nothing in the array, return nothing
            if (!count($tags)) {
                print "---";
            } else {
                // create our new array
                $arrDisplay = array();
                
                // loop through our array and update the text to a full link
                foreach ($tags as $tag) {
                    $arrDisplay[] = "<a href='/blogs/index.php?tag=" . urlencode(trim($tag)) . "' title='Read posts tagged as " . $tag . "'>" . trim($tag) . "</a>";
                }
                
                // return the array
                print implode(", ", $arrDisplay);
            }
        }
        
        // our function to display the teaster text for the blog list
        function displayBlogTeaser($text) {
            // see if the BBCode parser has been set
            if (method_exists($this->parser, "qParse")) {
                $text = trim($this->parser->qParse($text));
            } else {
                $text = trim(strip_tags($text));
            }
            
            // get our total number of words in the post
            $count = str_word_count($text);
            
            // if the # is less than the entered teaser length, display the whole thing
            if ($count <= $this->teaserLength) {
                $teaser = $text;
            } else {
                // create an array of text from our string, split on a space
                $arrWords = explode(" ", $text);
                $teaser = "";
                
                // loop through our words to build our teaser
                for ($i = 0; $i < $this->teaserLength; $i++) {
                    $teaser .= " " . $arrWords[$i];
                }
            }
            
            // print the teaser
            print trim($teaser) . "...\n";
        }
        
        /**
        * CODE TO PROCESS AND DISPLAY COMMENTS FOR POSTS
        */
        
        // our crud interface
        function addBlogComment($id, $title, $post, $date, $owner) {
            // set our content
            $this->cBlog      = $id;
            $this->cTitle     = $this->validateBlogTitle($title);
            $this->cText      = $this->validateBlogText($post);
            $this->cPostDate  = $date;
            $this->cOwner     = $owner;
            
            // save the data into the db
            $this->runCommentUpdate('add');
        }
        
        // our function to update the vars for this specific post
        function updateBlogComment($id, $title, $post, $owner, $user) {
            // make sure this user can edit this info
            $this->validateBlogOwner($user, $owner);
            
            // set our content
            $this->cID     = $id;
            $this->cTitle  = $this->validateBlogTitle($title);
            $this->cText   = $this->validateBlogText($post);
            $this->cOwner  = $owner;
            
            // save the data into the db
            $this->runCommentUpdate('update');
        }
        
        // our function to delete a single comment
        function deleteBlogComment($id, $owner, $user) {
            // make sure this user can edit this info
            $this->validateBlogOwner($user, $owner);
            
            // set our content
            $this->cID     = $id;
            $this->cOwner  = $owner;
            
            // save the data into the db
            $this->runCommentUpdate('delete');
        }
        
        // our function to remove all comments for a post
        function deleteAllBlogComments() {
            // run the SQL to remove all comments for the set post
            $qryDelete = $this->dbConn->query("DELETE FROM `" . $this->tableComments . "` WHERE bid = '" . $this->bID . "'");
        }
        
        // our function to run our update into the db
        function runCommentUpdate($command) {
            // see if we need to stop
            $this->checkBlogError();
            
            // based on our passed command, continue
            switch ($command) {
                case "add":
                    // add the data into the db
                    $qryAdd = $this->dbConn->query("INSERT INTO `" . $this->tableComments . "` ( title, comment, bid, uid, postdate ) VALUES ( '" . $this->cTitle . "', '" . $this->cText . "', '" . $this->cBlog . "', '" . $this->cOwner . "', '" . $this->cPostDate . "' )");
                    
                    // if it was added, we're all good
                    if (mysql_insert_id($this->dbConn->connection)) {
                        // set the blog ID
                        $this->cID = mysql_insert_id($this->dbConn->connection);
                    } else {
                        // set the error message
                        $this->errorText = "The comment could not be added.  Please try again";
                        $this->checkBlogError();
                    }
                    
                    break;
                
                case "update":
                    // update the db with the new passed data
                    $qryUpdate = $this->dbConn->query("UPDATE `" . $this->tableComments . "` SET title = '" . $this->cTitle . "', comment = '" . $this->cText . "' WHERE id = '" . $this->cID . "' AND uid = '" . $this->cOwner . "' LIMIT 1");
                    
                    break;
                
                case "delete":
                    // update the db with the new passed data
                    $qryDelete = $this->dbConn->query("DELETE FROM `" . $this->tableComments . "` WHERE id = '" . $this->cID . "' AND uid = '" . $this->cOwner . "' LIMIT 1");
                    
                    // see if it was added
                    if (!$this->dbConn->affectedRows()) {
                        // set the error message
                        $this->errorText = "The comment was not deleted successfully.";
                        $this->checkBlogError();
                    }
                    
                    break;
            }
        }
        
        // our function to display our comments for a chosen blog
        function displayBlogComments($blog, $user = 0) {
            // create our array from our data
            $arrComments = $this->getBlogComments($blog);
            
            // see if we have any data to display
            if (count($arrComments)) {
                // loop through our array and display the content
                for ($i = 0; $i < count($arrComments); $i++) {
                    ?>
                    <div class="blog_comment">
                        <div class="blog_comment_title"><h3><?php print $arrComments[$i]["title"]; ?></h3></div>
                        <div class="blog_comment_owner">Posted by <?php print $arrComments[$i]["username"]; ?> on <?php print $arrComments[$i]["postdate"]; ?>
                    <?php
                        // if this is the owner, allow them to edit their post
                        if ($user && $arrComments[$i]["uid"] == $user) {
                            ?> | 
                            <a href="/blogs/comment.php?id=<?php print $arrComments[$i]["id"]; ?>&action=edit" title="Edit Your Comment" class="blog_edit">Edit</a> | 
                    <a href="/blogs/comment.php?id=<?php print $arrComments[$i]["id"]; ?>&action=delete" onclick="return confirm('Are you sure you want to remove this comment?  This cannot be undone.');" title="Delete Your Comment" class="blog_delete">Delete</a>
                            <?php
                        }
                    ?></div>
                        <div class="blog_comment_text"><?php print $this->convertLineBreaks($arrComments[$i]["comment"]); ?></div>
                    </div>
                    <?php
                }
            }
        }
        
        // our function to display posts, based on user selection (if any)
        function getBlogComments($blog) {
            // query the db for data we need to display
            $qryComments = $this->dbConn->query("SELECT " . $this->tableComments . ".*, " . $this->tableMembers . ".ID, " . $this->tableMembersName . " as username FROM " . $this->tableComments . ", " . $this->tableMembers . " WHERE " . $this->tableComments . ".bid = '" . $blog . "' AND " . $this->tableComments . ".uid = " . $this->tableMembers . ".ID ORDER BY " . $this->tableComments . ".postdate ASC");
            
            // create an array to display
            return $this->createBlogCommentsArray($qryComments);
        }
        
        // our function to display posts, based on a passed tag
        function getBlogCommentArray($id, $user = 0) {
            // query the db for data we need to display
            $qryComment = $this->dbConn->query("SELECT " . $this->tableComments . ".*, " . $this->tableMembers . ".ID, " . $this->tableMembersName . " as username FROM " . $this->tableComments . ", " . $this->tableMembers . " WHERE " . $this->tableComments . ".id = '" . $id . "' AND " . $this->tableComments . ".uid = '" . $user . "'AND " . $this->tableComments . ".uid = " . $this->tableMembers . ".ID  LIMIT 1");
            
            // return our array
            return $this->createBlogCommentsArray($qryComment);
        }
        
        // our function to create our array of data to display
        function createBlogCommentsArray($qryComments) {
            // create our array of posts
            $arrComments = array();
            
            // if we have results, continue
            if ($qryComments->numRows()) {
                // loop through our results and add to our array
                while ($qryRow = $qryComments->fetchRow(DB_FETCHMODE_ASSOC)) {
                    // see if the BBCode parser has been set
                    if (method_exists($this->parser, "qParse")) {
                        $comment = $this->parser->qParse($qryRow["comment"]);
                    } else {
                        $comment = trim($qryRow["comment"]);
                    }
                    
                    // add all of the data to the array
                    $arrComments[] = array(
                                        "id"        => $qryRow["id"],
                                        "title"     => $qryRow["title"],
                                        "comment"   => $comment,
                                        "bid"       => $qryRow["bid"],
                                        "uid"       => $qryRow["uid"],
                                        "postdate"  => date($this->dateFormat, strtotime($qryRow["postdate"])),
                                        "uid"       => $qryRow["uid"],
                                        "username"  => $qryRow["username"]
                                         );
                }
            }
            
            // return the full array
            return $arrComments;
        }
        
        /**
        * TAG CLOUD FUNCTIONS
        */
        
        // our function to display our tag cloud
        function displayTagCloud() {
            // get our array of tags to display
            $cloud = $this->getAllTags();
            
            // build our HTML to display the cloud
            $this->displayTagCloudHTML($cloud);
        }
        
        // our function to display our tag cloud
        function displayUserCloud() {
            // get our array of tags to display
            $cloud = $this->getAllUsers();
            
            // build our HTML to display the cloud
            $this->displayUserCloudHTML($cloud);
        }
        
        // the function to display the HTML for our cloud array
        function displayTagCloudHTML($cloud) {
            if (count($cloud)) {
                ?>
                <div id="cloud_tag">
                    <ol class='tag-cloud'>
                        <b class="head">Popular Tags</b><br />
                        <?php
                            // loop through our array
                            for ($i = 0; $i < count($cloud); $i++) {
                                ?>
                                <li class="<?php print $this->displayTagStyle($cloud[$i]["count"]); ?>"><span><?php print $cloud[$i]["count"]; ?> posts are tagged with </span><a href="/blogs/index.php?tag=<?php print urlencode($cloud[$i]["tag"]); ?>" class="tag"><?php print $cloud[$i]["tag"]; ?></a></li>
                                <?php
                            }
                        ?>
                    </ol>
                </div>
                <?php
            }
        }
        
        // the function to display the HTML for our cloud array
        function displayUserCloudHTML($cloud) {
            if (count($cloud)) {
                ?>
                <div id="cloud_user">
                    <ol class='tag-cloud'>
                        <b class="head">Popular Bloggers</b><br />
                        <?php
                            // loop through our array
                            for ($i = 0; $i < count($cloud); $i++) {
                                ?>
                                <li class="<?php print $this->displayTagStyle($cloud[$i]["count"]); ?>"><span><?php print $cloud[$i]["count"]; ?> posts are tagged with </span><a href="/blogs/index.php?id=<?php print $cloud[$i]["uid"]; ?>" class="tag"><?php print $cloud[$i]["user"]; ?></a></li>
                                <?php
                            }
                        ?>
                    </ol>
                </div>
                <?php
            }
        }
        
        // our function to create an array of all tags
        function getAllTags() {
            // query the db for our tags and their count
            $qryTags = $this->dbConn->query("SELECT " . $this->tableTags . ".tag, COUNT(*) AS totals from " . $this->tableTags . " GROUP BY tag LIMIT 50");
            
            // create our array of tags and their totals
            $arrTags = array();
            
            // loop through our array
            while ($qryRow = $qryTags->fetchRow(DB_FETCHMODE_ASSOC)) {
                $arrTags[] = array("tag" => $qryRow["tag"],
                                   "count" => $qryRow["totals"]
                                  );
            }
            
            // return the array
            return $arrTags;
        }
        
        // our function to create an array of all users who have posted
        function getAllUsers() {
            // query the db for our tags and their count
            $qryTags = $this->dbConn->query("SELECT " . $this->tableMembersID . " AS uid, " . $this->tableMembersName . " AS user, COUNT(" . $this->tableBlogs . ".uid) AS totals FROM " . $this->tableMembers . ", " . $this->tableBlogs . " WHERE " . $this->tableMembersID . " = " . $this->tableBlogs . ".uid GROUP BY " . $this->tableMembersName . " LIMIT 50");
            
            // create our array of tags and their totals
            $arrTags = array();
            
            // loop through our array
            while ($qryRow = $qryTags->fetchRow(DB_FETCHMODE_ASSOC)) {
                $arrTags[] = array("user"  => $qryRow["user"],
                                   "uid"   => $qryRow["uid"],
                                   "count" => $qryRow["totals"]
                                  );
            }
            
            // return the array
            return $arrTags;
        }
        
        // display our appropriate style based on the number of tags found
        function displayTagStyle($total) {
            // based on the # of entries, display a total
            if ($total == 1) {
                return "cloud1";
            } else if ($total == 2) {
                return "cloud2";
            } else if ($total >= 3 && $total <= 5) {
                return "cloud3";
            } else if ($total >= 6 && $total <= 10) {
                return "cloud4";
            } else if ($total >= 11 && $total <= 20) {
                return "cloud5";
            } else if ($total > 20) {
                return "cloud6";
            }
        }
        
        /**
        * VALIDATION AND OTHER FUNCTIONS
        */
        
        // validate the title
        function validateBlogTitle($title) {
            // make sure it not empty and of proper length
            if (empty($title) || strlen($title) > $this->titleLength) {
                $this->bTitle = '';
                $this->errorText = 'Your title is too long \(' . strlen($title) . ' chars\).  Please shorten it to less than ' . $this->titleLength . ' chars.';
                return "";
            } else {
                return strip_tags(trim($title));
            }
        }
        
        // validate the text
        function validateBlogText($text) {
            // make sure it not empty and of proper length
            if (empty($text)) {
                $this->bText = '';
                $this->errorText = 'Your post contains no data.  Please add something to your post.';
                return "";
            } else {
                return strip_tags(trim($text));
            }
        }
        
        // validate the text
        function validateBlogOwner($uid, $owner) {
            // make sure it not empty and of proper length
            if ($uid != $owner) {
                $this->errorText = 'Your do not have permission to edit this post, as you are not the owner.';
            }
        }
        
        // change any line breaks to <br /> tags in the code
        function convertLineBreaks($text) {
            return str_replace(chr(10), "<br />", $text);
        }
        
        // throw an error
        function checkBlogError() {
            // see if we need to throw an error
            if (!empty($this->errorText)) {
                // display our error and quit
                print '<script language="JavaScript">alert("' . $this->errorText . '"); history.back();</script>' . "\n";
                exit();
            }
        }
    }
?>