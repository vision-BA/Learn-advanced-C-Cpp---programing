<?php
/**
 * Footer - SIMS
 */
?>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-section">
            <h4>About SIMS</h4>
            <p>Student Information Management System - A comprehensive platform for managing student data, courses, grades, and more.</p>
        </div>

        <div class="footer-section">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">Documentation</a></li>
                <li><a href="#">Support</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Contact Info</h4>
            <p>Email: support@sims.com</p>
            <p>Phone: +1-800-SIMS-HELP</p>
            <p>Address: 1234 Education Lane, Campus City, CC 12345</p>
        </div>

        <div class="footer-section">
            <h4>Follow Us</h4>
            <div class="social-links">
                <a href="#">Facebook</a>
                <a href="#">Twitter</a>
                <a href="#">LinkedIn</a>
                <a href="#">Instagram</a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; 2024 Student Information Management System. All rights reserved.</p>
        <p>Version 1.0.0 | Last Updated: March 2024</p>
    </div>
</footer>

<style>
    .footer {
        background: #2d3748;
        color: #e2e8f0;
        margin-top: 50px;
        padding: 40px 20px 20px;
    }

    .footer-content {
        max-width: 100%;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 30px;
        margin-bottom: 30px;
    }

    .footer-section h4 {
        color: white;
        margin-bottom: 15px;
    }

    .footer-section p {
        margin: 8px 0;
        line-height: 1.6;
    }

    .footer-section ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-section li {
        margin: 8px 0;
    }

    .footer-section a {
        color: #a0aec0;
        text-decoration: none;
    }

    .footer-section a:hover {
        color: white;
    }

    .social-links {
        display: flex;
        gap: 15px;
    }

    .footer-bottom {
        border-top: 1px solid #4a5568;
        padding-top: 20px;
        text-align: center;
        color: #718096;
    }

    .footer-bottom p {
        margin: 5px 0;
        font-size: 12px;
    }
</style>
