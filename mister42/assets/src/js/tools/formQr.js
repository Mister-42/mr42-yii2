$("#qr-type").on('change', function () {
	$('[class*="field-qr-"]').addClass('hidden');
	$('.field-qr-type').removeClass('hidden');

	if ($(this).val() === 'BookMarkFormat') {
		$('.field-qr-title, .field-qr-url').removeClass('hidden');
	} else if ($(this).val() === 'BtcFormat') {
		$('.field-qr-address, .field-qr-amount, .field-qr-name, .field-qr-msg').removeClass('hidden');
	} else if ($(this).val() === 'GeoFormat') {
		$('.field-qr-lat, .field-qr-lng, .field-qr-altitude').removeClass('hidden');
	} else if ($(this).val() === 'iCalFormat') {
		$('.field-qr-summary, .field-qr-dtstart, .field-qr-dtend').removeClass('hidden');
	} else if ($(this).val() === 'MailMessageFormat') {
		$('.field-qr-email, .field-qr-subject, .field-qr-msg').removeClass('hidden');
	} else if ($(this).val() === 'MailToFormat') {
		$('.field-qr-email').removeClass('hidden');
	} else if ($(this).val() === 'MeCardFormat') {
		$('.field-qr-firstname, .field-qr-lastname, .field-qr-sound, .field-qr-phone, .field-qr-videophone, .field-qr-email, .field-qr-note, .field-qr-birthday, .field-qr-address, .field-qr-url, .field-qr-nickname').removeClass('hidden');
	} else if ($(this).val() === 'MmsFormat') {
		$('.field-qr-phone, .field-qr-msg').removeClass('hidden');
	} else if ($(this).val() === 'PhoneFormat' || $(this).val() === 'SmsFormat') {
		$('.field-qr-phone').removeClass('hidden');
	} else if ($(this).val() === 'vCardFormat') {
		$('.field-qr-name, .field-qr-fullname, .field-qr-email').removeClass('hidden');
	} else if ($(this).val() === 'WifiFormat') {
		$('.field-qr-authentication, .field-qr-ssid, .field-qr-password, .field-qr-hidden').removeClass('hidden');
	} else if ($(this).val() === 'YoutubeFormat') {
		$('.field-qr-videoid').removeClass('hidden');
	}

	if ($(this).val() !== '') {
		$('.field-qr-size, .field-qr-margin, .field-qr-buttons').removeClass('hidden');
	}
}).change();
