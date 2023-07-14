@echo off
IF NOT EXIST .\.env (
  echo Copying .env.example to .env
  copy tools\.env.example .\.env
)

IF NOT EXIST authors (
  mkdir authors
)

IF NOT EXIST blogs (
  mkdir blogs
)

docker build -t blogpop-manager .
