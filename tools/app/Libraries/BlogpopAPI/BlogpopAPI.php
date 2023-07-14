<?php

namespace App\Libraries\BlogpopAPI;

use App\Libraries\BlogpopAPI\Entities\Authors;
use App\Libraries\BlogpopAPI\Entities\Blogs;
use App\Libraries\BlogpopAPI\Entities\Posts;

class BlogpopAPI
{
    public function __construct(protected Authors $authors, protected Blogs $blogs, protected Posts $posts) {}

    public function authors(): Authors
    {
        return $this->authors;
    }

    public function blogs(): Blogs
    {
        return $this->blogs;
    }

    public function posts(): Posts
    {
        return $this->posts;
    }

}
