<?php
/**
 * Global constants used across the NEXERA application.
 */

declare(strict_types=1);

namespace Nexera\Config;

// Application metadata
const APP_NAME = 'NEXERA';
const APP_TAGLINE = 'A Modern College Management System to Connect Students, Parents, and Staff Seamlessly.';

// Session configuration
const SESSION_NAME = 'nexera_session';
const SESSION_LIFETIME = 3600; // 1 hour

// CSRF configuration
const CSRF_TOKEN_KEY = 'csrf_token';

// Upload configuration
const UPLOAD_MAX_BYTES = 5 * 1024 * 1024; // 5 MB
const UPLOAD_ALLOWED_EXTENSIONS = [
    'pdf',
    'doc',
    'docx',
    'ppt',
    'pptx',
    'xls',
    'xlsx',
    'zip',
    'rar',
    'jpg',
    'jpeg',
    'png',
    'gif',
];

// Roles
const ROLE_ADMIN = 'admin';
const ROLE_STAFF = 'staff';
const ROLE_STUDENT = 'student';
const ROLE_PARENT = 'parent';

// Staff categories
const STAFF_TEACHING = 'teaching';
const STAFF_NON_TEACHING = 'non_teaching';


