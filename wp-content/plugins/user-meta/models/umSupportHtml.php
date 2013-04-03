<?php

if( !class_exists( 'umSupportHtml' ) ) :
class umSupportHtml {
    
    function boxHowToUse(){
        global $userMeta;
        
        $html = null;
        $html .= sprintf( __( '<p><strong>Step 1.</strong> Create Field from User Meta %s page.</p>', $userMeta->name ), $userMeta->adminPageUrl('fields_editor') );
        $html .= sprintf( __( '<p><strong>Step 2.</strong> Go to User Meta %s page. Choose a Form Name, drag and drop fields from right to left and save the form.</p>', $userMeta->name ), $userMeta->adminPageUrl('forms_editor') );
        $html .= __( '<p><strong>Step 3.</strong> Write shortcode to your page or post. Shortcode (e.g.): &#91;user-meta type="profile" form="your_form_name"&#93;</p>', $userMeta->name);
        $html .= "<div><center><a class=\"button-primary\" href=\"" . $userMeta->website .  "\">". __( 'Visit Plugin Site', $userMeta->name ) ."</a></center></div>";
        return $html;
    }
    
    function boxGetPro(){
        global $userMeta;
        
        $html = null;
        $html .= "<div style='padding-left: 10px'>";
        $html .= "<p>Get <strong>User Meta Pro</strong> for : </p>";
        $html .= "<li>Frontend Registration.</li>";
        $html .= "<li>Frontend Login shortcode and widget.</li>";
        $html .= "<li>Allow user to login with their Email or Username.</li>";
        $html .= "<li>Add extra fields to backend profile.</li>";
        $html .= "<li>Role based user redirection on login, logout and registratioin.</li>";
        $html .= "<li>User activatation/deactivation, Admin approval on new user registration.</li>";
        $html .= "<li>Customize email notification with including extra field's data.</li>";
        $html .= "<li>Frontend password reset.</li>";
        $html .= "<p></p>";
        $html .= "<li>35 types of fields for creating profile/registration form.</li>";        
        $html .= "<li>Fight against spam by Captcha.</li>";
        $html .= "<li>Brake your form into multiple page.</li>";
        $html .= "<li>Group fields using Section Heading.</li>";
        $html .= "<li>Write your own html by Custom HTML.</li>";
        $html .= "<li>Allow user to upload their file by File Upload.</li>";
        $html .= "<li>Country Dropdown for country selection.</li>";        
        $html .= "<br />";
        $html .= "<center><a class='button-primary' href='http://user-meta.com'>Get User Meta Pro</a></center>";
        $html .= "</div>";
        return $html;
    }    
    
    function boxShortcodesDocs(){
        global $userMeta;
        
        $html = null;
        $html .= "<div style='padding-left: 10px'>";
        $html .= __( '<p><strong>&#91;user-meta type="type_name" form="your_form_name"&#93;</strong></p>', $userMeta->name );
        $html .= __( '<li><strong>type="profile"</strong> for showing profile form.</li>', $userMeta->name );        
        $html .= __( '<li><strong>type="registration"</strong> for showing registration form.</li>', $userMeta->name );
        $html .= __( '<li><strong>type="profile-registration"</strong> for showing profile form if user logged in, or showing registration form, if not user logged in.</li>', $userMeta->name );
        $html .= __( '<li><strong>type="public"</strong> for showing public profile if user_id parameter provided as GET reguest.</li>', $userMeta->name );
        $html .= __( '<li><strong>type="login"</strong> for showing login page.</li>', $userMeta->name );
        $html .= "<p></p>";
        $html .= __( '<p>N.B. "registration", "both" and "login" is only supported in pro version.</p>', $userMeta->name );
        $html .= __( '<p>Admin user can see all others frontend profile from User Administration screen. To enable this feature, go to User Meta &#62;&#62; Settings, select profile page from Profile Page Selection and enable right sided checkbox.</p>', $userMeta->name );
        $html .= __( '<p>In Case of extra field, you need to define unique meta_key. That meta_key will be use to save extra data in usermeta table. Without defining meta_key, extra data won\'t save.</p>', $userMeta->name );
        $html .= "<center><a class='button-primary' href='http://user-meta.com/documentation/'>". __( 'Read More', $userMeta->name ) ."</a></center>";
        $html .= "</div>";
        return $html;        
    }
    
    function getProLink( $label=null ){
        global $userMeta;
        
        $label = $label ? $label : $userMeta->website;
        return "<a href=\"{$userMeta->website}\">$label</a>";
    }    
}
endif;
