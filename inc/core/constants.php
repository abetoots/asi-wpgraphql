<?php

namespace ASI_WPGraphQL\Constants;

//!DO NOT MODIFY
//WP GraphQl
const JWT_AUTH_EXPIRATION = "jwtAuthExpiration";
const REFRESH_TOKEN = "refreshToken";
const AUTH_TOKEN = "authToken";

//Plugin
const PLUGIN_PREFIX = 'asi-wpgraphql';
const PLUGIN_HTML_TEMPLATES_DIR = ASI_WPGRAPHQL_PLUGIN_DIR . 'frontend/html-templates/';
const ACTION_AJAX_UPLOAD_FILE = 'asi_wpgraphql_do_action_upload_file';

//Roles
const ASI_MEMBER = 'asi_member';
const ASI_ADMIN = 'asi_admin';

//User Database keys
const FULL_NAME = "full_name";
const BUSINESS_NAME = 'business_name';
const BUSINESS_DESCRIPTION = 'business_description';
const BUSINESS_CATEGORIES = 'business_categories';
const STREET = 'street';
const CITY = 'city';
const PROVINCE = 'province';
const COUNTRY = 'country';
const PHONE_NUM = 'phone_num';
const TEL_NUM = 'tel_num';
const SOCIAL_WEBSITE = 'social_website';
const SOCIAL_FACEBOOK = 'social_facebook';
const PROFILE_PHOTO = 'profile_photo';
const ATTACHMENT_ID = 'attachment_id';

//GraphQl
const AIM_REGISTER_ASI_MEMBER = 'create_asi_member';
const ASI_ROLE = 'asi_role';
const TYPE_PROFILE_PHOTO = 'ProfilePhoto';
const ATTACHMENT_ID_DEFAULT = -1;
