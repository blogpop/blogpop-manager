@echo off
copy .\.env tools\.env

docker run -it --rm -v %cd%:/app -v vendor_vol:/app/tools/vendor blogpop-manager php tools/tools boot %*
