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

Requiring authentication
=

In order to require authentication before loading a page, in **security.yaml**, under **access_control:** \
add the following line: \
`- { path: regex_for_url, roles: required_role_for_access }`\
where :
 - *regex_for_url* - regular expression matching the URL
 - *required_role_for_access* - role user must belong to in order to be granted access\
                                (currently only ROLE_USER is handled.)
