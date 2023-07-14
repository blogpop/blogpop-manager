<?php

namespace App\Libraries\BlogpopAPI\Traits;

use App\Libraries\BlogpopAPI\BlogpopAPI;

trait WithBlogpopAPI
{
    private BlogpopAPI $blogpopAPI;

    public function blogpop(): BlogpopAPI
    {
        if(!isset($this->blogpopAPI)){
            $this->blogpopAPI = app(BlogpopAPI::class);
        }

        return $this->blogpopAPI;
    }

}
