Blogpop Manager is a utility for managing your blogpop blogs.

This project is a wrapper around the Laravel Zero project.  The Laravel Zero project is a robust open source tool that provides lots of extensibility.
This should be enough to get you going but feel free to add your own commands.
https://github.com/owenvoke/laravel-zero-docker

# Installation

1.  Install Docker (https://www.docker.com/products/docker-desktop/)
2.  Run the install script
3.  Run the auth command with your blogpop api key

## Install Script
```bash
./install.sh
```

## Auth Command
```bash
./blogpop auth <api_key>
```

# Blogpop CLI

You can use the blogpop CLI tool to generate new authors, blogs, and posts.  You can additionally sync your blogs with blogpop using this tool.
```bash
./blogpop <command> [options]
```

## Commands

| Command | Description |
| --- | --- |
| auth | Authenticate with blogpop |
| help:me | Print out the help screen |
| sync | Syncs your blogs with the blogpop server |
| new:author | Creates a new author |
| new:blog | Creates a new blog |
| new:post | Creates a new post |


### Sync
The sync command will download all the authors, blogs and posts in your account into the [root]/authors and [root]/blogs directories.
It will additionally create any new authors, blogs and posts that are in the file system that don't exist in blogpop.
If there are updates locally it will update blogpop with those updates.  If there are updates remotely it will download those changes.
```bash
./blogpop sync
```


### New Author
This command will scaffold a new author in your authors directory. It will create a new directory with the author's name and a new author.json file.
From there you will need to fill in details about the author before you will be able to sync it.

```bash
./blogpop new:author --name="Author's Name"
```

### New Blog
This command will scaffold a new blog in your blogs directory. It will create a new directory based on the blogs's title and a new blog.json file.
From there you will need to fill in details about the blog before you will be able to sync it.

```bash
./blogpop new:blog --title="Blog's title"
```


### New Post
This command will scaffold a new blog post for a given blog. It will create a new directory based on the the post's title and a new post.json file.
From there you will need to fill in details about the post before you will be able to sync it.

```bash
./blogpop new:blog --blog="blog-name-slug" --title="Post's title" --author="author-name-slug"
```