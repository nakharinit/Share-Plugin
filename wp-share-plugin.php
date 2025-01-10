<?php
/*
Plugin Name: WP-Share-Plugin
Description: A WordPress plugin that adds social share buttons (Facebook, Twitter, LINE, and Copy link) to posts.
Version: 1.2
Author: Nakharin
Plugin URI: https://github.com/nakharinit/WP-Share-Plugin
License: GPL2
*/

// Enqueue necessary styles and scripts only for posts
function custom_share_plugin_enqueue_scripts() {
    // Check if it's a single post
    if (is_single()) {
        // Font Awesome CSS
        wp_enqueue_style('fontawesome-css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');
        
        // Custom Styles
        wp_add_inline_style('fontawesome-css', '
            .share-container {
                display: flex;
                align-items: center;
                gap: 15px;
                margin-top: 15px;
                flex-wrap: wrap; /* ช่วยให้ปุ่มอยู่ในแถวเดียวกันหรือแถวใหม่เมื่อพื้นที่ไม่พอ */
                justify-content: flex-start; /* จัดให้เริ่มจากซ้าย */
            }
            .share-label {
                font-weight: bold;
                margin-right: 10px;
                font-size: 14px;
                display: block;
                margin-bottom: 5px; /* ให้แยกจากปุ่มเมื่อจำเป็น */
            }
            .share-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 50px;  /* ขนาดปุ่มที่เหมาะสม */
                height: 50px; /* ขนาดปุ่มที่เหมาะสม */
                border-radius: 50%;
                color: white;
                text-decoration: none;
                font-size: 24px; /* ขนาดไอคอนที่เหมาะสม */
                transition: all 0.3s ease;
                box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
                flex-shrink: 0; /* ป้องกันการย่อขนาดไอคอนเมื่อพื้นที่ไม่พอ */
            }
            .share-btn:hover {
                transform: scale(1.1);
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            }
            .facebook { background-color: #1877F2; }
            .twitter { background-color: #1DA1F2; }
            .line { background-color: #00C300; }
            .copy-btn {
                background-color: #f5f5f5;
                color: #333;
                border-radius: 20px;
                padding: 12px 20px;
                font-size: 14px;
                cursor: pointer;
                border: none;
                transition: all 0.3s ease;
                box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
            }
            .copy-btn:hover {
                background-color: #ddd;
                transform: scale(1.05);
            }

            /* สำหรับอุปกรณ์ที่มีหน้าจอเล็ก (Tablet และมือถือ) */
            @media (max-width: 1024px) {
                .share-btn {
                    width: 45px;
                    height: 45px;
                    font-size: 22px; /* ปรับขนาดไอคอนบนอุปกรณ์ที่มีหน้าจอเล็ก */
                }

                .copy-btn {
                    padding: 10px 18px;
                    font-size: 12px;
                }

                .share-label {
                    font-size: 12px;
                    margin-bottom: 10px;
                }
            }

            /* สำหรับอุปกรณ์มือถือ */
            @media (max-width: 768px) {
                .share-container {
                    flex-direction: row;
                    justify-content: space-evenly; /* ให้ปุ่มจัดอยู่ในแถวเดียวกัน */
                }

                .share-btn {
                    width: 40px;
                    height: 40px;
                    font-size: 20px; /* ปรับขนาดไอคอนบนมือถือ */
                }

                .copy-btn {
                    padding: 8px 14px;
                    font-size: 12px;
                    margin-top: 8px;
                }
            }

            /* สำหรับมือถือหน้าจอเล็กมาก */
            @media (max-width: 480px) {
                .share-btn {
                    width: 35px !important;
                    height: 35px !important;
                    font-size: 18px !important; /* ลดขนาดไอคอนบนมือถือ */
                }

                .copy-btn {
                    padding: 8px 12px;
                    font-size: 10px; /* ปรับขนาดฟอนต์บนมือถือหน้าจอเล็ก */
                }
            }
        ');
    }
}
add_action('wp_enqueue_scripts', 'custom_share_plugin_enqueue_scripts');

// Function to generate the share buttons
function custom_share_buttons($content) {
    // Check if it is a single post (not a page)
    if (is_single()) {
        // Get the current post URL
        $current_url = esc_url(get_permalink());

        // Share buttons HTML
        $share_buttons = '
            <div class="share-container">
                <span class="share-label">แชร์:</span>
                <!-- Facebook Share -->
                <a href="https://www.facebook.com/sharer/sharer.php?u=' . $current_url . '" 
                   target="_blank" class="share-btn facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>

                <!-- Twitter Share -->
                <a href="https://twitter.com/share?url=' . $current_url . '&text=Check this out!" 
                   target="_blank" class="share-btn twitter">
                    <i class="fab fa-twitter"></i>
                </a>

                <!-- LINE Share -->
                <a href="https://line.me/R/msg/text/?' . $current_url . '" 
                   target="_blank" class="share-btn line">
                    <i class="fab fa-line"></i>
                </a>

                <!-- Copy Link -->
                <button id="copyLinkBtn" class="copy-btn" onclick="copyToClipboard()">
                    <i class="fas fa-copy" style="font-size: 16px; margin-right: 5px;"></i>คัดลอกลิงก์
                </button>
            </div>
        ';

        // Append share buttons to the content
        $content .= $share_buttons;
    }

    // Return the content with the buttons added
    return $content;
}
add_filter('the_content', 'custom_share_buttons');

// Add JavaScript to the footer to avoid conflicts
function custom_share_plugin_footer_script() {
    if (is_single()) {
        ?>
        <script>
            function copyToClipboard() {
                const url = "<?php echo esc_url(get_permalink()); ?>";
                const copyButton = document.getElementById("copyLinkBtn");

                navigator.clipboard.writeText(url).then(() => {
                    // Change button text to "คัดลอกแล้ว"
                    copyButton.innerHTML = "<i class='fas fa-check' style='font-size: 16px; margin-right: 5px;'></i>คัดลอกแล้ว!";

                    // Optional: Add a brief delay before resetting the button text
                    setTimeout(function() {
                        copyButton.innerHTML = "<i class='fas fa-copy' style='font-size: 16px; margin-right: 5px;'></i>คัดลอกลิงก์";
                    }, 2000);
                }).catch(err => {
                    console.error("เกิดข้อผิดพลาดในการคัดลอกลิงก์", err);
                });
            }
        </script>
        <?php
    }
}
add_action('wp_footer', 'custom_share_plugin_footer_script');
