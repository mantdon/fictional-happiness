Team
=
Martynas Treinys IFF-6/3\
Mantas Arlauskas IFF-6/3\
Mantvydas Donauskas IFF-6/3\
Dainius Arnastauskas IFF-6/3

Project set up
=
Run:\
 `composer install`\
 `yarn install`\
 `yarn run encore dev`
 

Open .env, find this line\
`DATABASE_URL=mysql://{db_user}:{db_password}@127.0.0.1:3306/fictional-happiness`\
and enter your mysql username and password.

Compiling assets
=

Every time javascript or css is changed it's necessary to compile it:\
`yarn run encore dev`