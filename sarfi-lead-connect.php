<?php
/*
Plugin Name: Sarfi Lead Connect
Description: Professional popup to connect via WhatsApp, LinkedIn or Email with animation and manual toggle button.
Version: 1.0
Author: Dev Sarfaraz
*/

// 1. Register Settings Page
add_action('admin_menu', 'sarfi_lead_menu');
function sarfi_lead_menu() {
    add_options_page(
        'Sarfi Lead Connect Settings',
        'Sarfi Lead Connect',
        'manage_options',
        'sarfi-lead-connect',
        'sarfi_lead_settings_page'
    );
}

// 2. Register Settings
add_action('admin_init', 'sarfi_lead_register_settings');
function sarfi_lead_register_settings() {
    register_setting('sarfi_lead_options', 'sarfi_lead_whatsapp');
    register_setting('sarfi_lead_options', 'sarfi_lead_linkedin');
    register_setting('sarfi_lead_options', 'sarfi_lead_email');
}

// 3. Settings Page Content
function sarfi_lead_settings_page() {
    ?>
    <div class="wrap">
        <h1>Sarfi Lead Connect Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('sarfi_lead_options'); ?>
            <?php do_settings_sections('sarfi_lead_options'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">WhatsApp Number (with country code)</th>
                    <td><input type="text" name="sarfi_lead_whatsapp" value="<?php echo esc_attr(get_option('sarfi_lead_whatsapp')); ?>" class="regular-text" placeholder="e.g. 923001234567" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">LinkedIn Profile URL</th>
                    <td><input type="text" name="sarfi_lead_linkedin" value="<?php echo esc_attr(get_option('sarfi_lead_linkedin')); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Email Address</th>
                    <td><input type="email" name="sarfi_lead_email" value="<?php echo esc_attr(get_option('sarfi_lead_email')); ?>" class="regular-text" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// 4. Frontend Popup HTML + Logic
add_action('wp_footer', 'sarfi_lead_connect_display');
function sarfi_lead_connect_display() {
    $whatsapp = get_option('sarfi_lead_whatsapp');
    $linkedin = get_option('sarfi_lead_linkedin');
    $email = get_option('sarfi_lead_email');

    if (!$whatsapp || !$linkedin || !$email) return; // Skip if not set

    $whatsapp_link = "https://wa.me/$whatsapp?text=Hi%20Sarfaraz!%20I%20visited%20your%20site.";
    ?>
    <style>
        #sarfi-box { position: fixed; bottom: 30px; right: -350px; background: #fff; color: #333; padding: 20px; border-radius: 15px 0 0 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); z-index: 9999; max-width: 280px; transition: right 0.8s ease; font-family: 'Segoe UI', sans-serif; }
        #sarfi-box.active { right: 30px; }
        #sarfi-box h3 { margin-top: 0; font-size: 18px; color: #f63b57; }
        #sarfi-box p { margin-bottom: 15px; }
        #sarfi-box button { display: block; width: 100%; background-color: #f63b57; color: white; border: none; padding: 10px; margin: 5px 0; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: bold; transition: background 0.3s; }
        #sarfi-box button:hover { background-color: #d32f46; }
        #sarfi-close { position: absolute; top: 8px; right: 10px; font-size: 18px; color: #888; cursor: pointer; font-weight: bold; }
        #sarfi-close:hover { color: #f63b57; }
        #sarfi-toast { position: fixed; bottom: 100px; right: 40px; background: #28a745; color: white; padding: 12px 18px; border-radius: 8px; font-size: 14px; display: none; z-index: 10000; animation: fadeInOut 2s ease-in-out; }
        @keyframes fadeInOut { 0% {opacity: 0; transform: translateY(10px);} 10% {opacity: 1; transform: translateY(0);} 90% {opacity: 1;} 100% {opacity: 0; transform: translateY(-10px);} }
        #sarfi-toggle-button { position: fixed; bottom: 30px; right: 30px; background: #f63b57; color: #fff; padding: 10px 14px; border-radius: 50px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); cursor: pointer; z-index: 9999; font-weight: bold; display: none; }
        @media (max-width: 600px) {
            #sarfi-box { bottom: 20px; max-width: 90%; }
            #sarfi-toggle-button { bottom: 20px; right: 20px; }
        }
    </style>

    <div id="sarfi-box">
        <span id="sarfi-close">&times;</span>
        <h3>Hi, I'm Dev Sarfraz 👋</h3>
        <p>Need help or want to collaborate? Let’s connect!</p>

        <a href="#" onclick="showToast('Redirecting to WhatsApp...'); setTimeout(() => { window.open('<?php echo esc_url($whatsapp_link); ?>', '_blank'); }, 1500); return false;">
            <button><img src="<?php echo plugin_dir_url(__FILE__); ?>images/whatsapp.png" width="20" style="vertical-align: middle; margin-right: 8px;"> WhatsApp Me</button>
        </a>

        <a href="#" onclick="showToast('Opening LinkedIn Profile...'); setTimeout(() => { window.open('<?php echo esc_url($linkedin); ?>', '_blank'); }, 1500); return false;">
            <button><img src="<?php echo plugin_dir_url(__FILE__); ?>images/linkedin.png" width="20" style="vertical-align: middle; margin-right: 8px;"> Connect on LinkedIn</button>
        </a>

        <a href="#" onclick="showToast('Launching Email...'); setTimeout(() => { window.location.href='mailto:<?php echo esc_attr($email); ?>'; }, 1500); return false;">
            <button><img src="<?php echo plugin_dir_url(__FILE__); ?>images/email.png" width="20" style="vertical-align: middle; margin-right: 8px;"> Email Me</button>
        </a>
    </div>

    <div id="sarfi-toast"></div>
    <div id="sarfi-toggle-button" onclick="toggleSarfiBox()">💬</div>

    <script>
        function showToast(message) {
            var toast = document.getElementById("sarfi-toast");
            toast.textContent = message;
            toast.style.display = "block";
            setTimeout(() => toast.style.display = "none", 2000);
        }

        function toggleSarfiBox() {
            const box = document.getElementById("sarfi-box");
            if (box.style.display === "none" || !box.classList.contains("active")) {
                box.style.display = "block";
                box.classList.add("active");
            } else {
                box.style.display = "none";
                box.classList.remove("active");
            }
        }

        window.addEventListener("load", function () {
            setTimeout(() => {
                if (!sessionStorage.getItem("sarfi_dismissed")) {
                    document.getElementById("sarfi-box").classList.add("active");
                } else {
                    document.getElementById("sarfi-toggle-button").style.display = "block";
                }
            }, 3000);

            document.getElementById("sarfi-close").addEventListener("click", function () {
                document.getElementById("sarfi-box").style.display = "none";
                document.getElementById("sarfi-toggle-button").style.display = "block";
                sessionStorage.setItem("sarfi_dismissed", "true");
            });
        });
    </script>
    <?php
}
