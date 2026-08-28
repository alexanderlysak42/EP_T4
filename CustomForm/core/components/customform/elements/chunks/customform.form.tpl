<form class="customform" method="post" action="[[~[[*id]]]]">
    <div class="customform__field">
        <label for="customform-name">Ім'я</label>
        <input type="text" id="customform-name" name="name" value="[[+name]]" required>
        <div class="error">[[+error_name]]</div>
    </div>

    <div class="customform__field">
        <label for="customform-phone">Телефон</label>
        <input type="tel" id="customform-phone" name="phone" value="[[+phone]]" placeholder="+380XXXXXXXXX" required>
        <div class="error">[[+error_phone]]</div>
    </div>

    <div class="customform__field">
        <label for="customform-email">Email</label>
        <input type="email" id="customform-email" name="email" value="[[+email]]">
        <div class="error">[[+error_email]]</div>
    </div>

    <div class="customform__field">
        <label for="customform-message">Повідомлення</label>
        <textarea id="customform-message" name="message" rows="4">[[+message]]</textarea>
    </div>

    <div class="customform__field error">[[+error_global]]</div>

    <div class="customform__honeypot">
        <label for="customform-website">Website</label>
        <input type="text" id="customform-website" name="website" tabindex="-1" autocomplete="off">
    </div>

    <button type="submit">Надіслати</button>
</form>
