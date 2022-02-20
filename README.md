# 3cket

Hello, this exercise was made for 3cket

### Objectives

- Image must not contain more than 204800 bytes
- You must have to use Composer to install the necessary dependencies
- You should investigate a Library to help you resizing the images
- Formats you should generate are the following formats: webp, compressed, and store the originals in a different folder
- Sizes possibility:
    - 310x150
    - 1920x1080	

### Requirements for a proper use

- PHP 7.4
- Composer
- PHP-Imagick module enabled
- Symfony CLI (For server start)
- Insomnia/Postman to test the microsservice endpoint

### How to test this Microsservice
Install symfony cli at https://symfony.com/download

Run:

`symfony server:start`

Open insomnia/postman and send a post request like: 


`
curl --request POST \
--url http://127.0.0.1:*PORT*/image-resize \
--header 'Content-Type: multipart/form-data; boundary=---011000010111000001101001' \
--form image=@*FILEPATH*.webp \
--form size=1
`

*Change *PORT* and *FILEPATH* to your server port and FILEPATH to any WEBP file, you can use the ones in the folder file_examples!

Thanks