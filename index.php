
<html>
	<head>
		<script src="https://beta.ekoconnect.in/widget/import-eko-connect-widget.js"></script>
	</head>

	<body>

		<?php
			$key = "f74c50a1-f705-4634-9cda-30a477df91b7";
			// Encode it using base64
			$encodedKey = base64_encode($key);
			// Get current timestamp in milliseconds since UNIX epoch as STRING
			// Check out https://currentmillis.com to understand the timestamp format
			$secret_key_timestamp = "".round(microtime(true) * 1000);
			// Computes the signature by hashing the salt with the encoded key 
			$signature = hash_hmac('SHA256', $secret_key_timestamp, $encodedKey, true);
			// Encode it using base64
			$secret_key = base64_encode($signature);

		?>
		<tf-eko-connect-widget

			id="ekowidget"
		     transaction-flow="remittance"
		     developer-key="becbbce45f79c6f5109f848acd540567"
		     secret-key="<?php echo $secret_key; ?>"
		     secret-key-timestamp="<?php echo $secret_key_timestamp; ?>"
		     initiator-id="9910028267"
		     merchant-document-id-type="1"
		     merchant-document-id="AABTG6381M"
		     pincode="122003"
		     language="en"
		     debug>
		</tf-eko-connect-widget>

	

	<script>
		const _key = "f74c50a1-f705-4634-9cda-30a477df91b7";
		const _themes = {
				1: { dark: false },
				2: { dark: true },
				3: { dark: false, colors: '{"--accent-color":"#7E57C2", "--light-accent-color":"#9575CD"}' },	// Purple Light		#9C27B0
				4: { dark: true, colors: '{"--accent-color":"#1DE9B6", "--light-accent-color":"#64FFDA"}' },	// Teal Dark
				5: { dark: true, colors: '{"--card-background-color":"#003466", "--light-card-background-color":"#004D96", "--accent-color":"#FFCC00", "--light-accent-color":"#FFDC52", "--menu-selected-background-color":"#3D6F9F", "--dark-menu-selected-background-color":"#285B8B", "--light-menu-selected-background-color":"#5581AA"}' },	// Eve Blue Dark
			};

		var _cust_colors = {
			"--accent-color":"#FF4081",
			"--card-background-color":"#FFFFFF"
		};

		var _cust_dark_base_theme = false;
		var _trxn_list = document.querySelectorAll('input[name="trxnlist"]');
		for(var i = 0; i < _trxn_list.length; i++) {
			_trxn_list[i].addEventListener('click', _selectTransaction);
		}

		// CAPTURE EKO WIDGET DEBIT-HOOK
		document.querySelector('#ekowidget').addEventListener('debit-hook', function(e) {
				console.warn("ON debit-hook >>>> ", e.detail);
				if (confirm("Debit-Hook Feature\n\nWith debit-hook feature, you can embed your own functionality before the transaction.\n\nEg: check your merchant's wallet balance before proceeding with the transaction. The widget will wait for your consent.\n\nWhen you are done, you may allow the widget to proceed or cancel the transaction.\n\nAllow transaction to proceed?"))
				{
					var timestamp = Date.now().toString();

					var secret_key = _genSecretHash(_key, timestamp);
					var prms = "";
					var req_hash = null;

					if (e.detail.interaction_type_id == 221 || e.detail.interaction_type_id == 279) {
						// Money Transfer (221) or Indo-Nepal (279)
						var d = e.detail.data;
						prms += (timestamp + d.customer_id + d.recipient_id + d.amount);
					} else if (e.detail.interaction_type_id == 149) {
						// Verify Recipient Bank Account
						prms += timestamp;
					}

					req_hash = _genSecretHash(_key, prms);
					document.querySelector('#ekowidget').go(true,		// , {request_hash: "WoArIAvm7CSNfHmX99AT5eThBzudcjndNH0jhMaPeV4="}
								{
									secretKey: secret_key,
									secretKeyTimestamp: timestamp,
									request_hash: req_hash
								});
				}
				else
				{
					document.querySelector('#ekowidget').go(false, {message: "You do not have sufficient balance"});
				}

			}.bind(this));


		// CAPTURE EKO WIDGET DEBIT-HOOK
		document.querySelector('#ekowidget').addEventListener('eko-request', function(e) {

				console.warn("[WIDGET] eko-request called >>>> ", e.detail);

			}.bind(this));


		// CAPTURE EKO WIDGET DEBIT-HOOK
		document.querySelector('#ekowidget').addEventListener('eko-response', function(e) {

				console.warn("[WIDGET] eko-response called >>>> ", e.detail);

			}.bind(this));


		// CAPTURE EKO WIDGET DEBIT-HOOK
		document.querySelector('#ekowidget').addEventListener('eko-network-error', function(e) {

				console.error("[WIDGET] eko-network-error called >>>> ", e.detail);

			}.bind(this));


		/*
		// Manually clear offline cache for widget. May be used for debugging
		function _clearCache()
		{
			document.querySelector('#ekowidget').clearCache();
		}
		*/

		function _selectTransaction()
		{

			var _trxn_flow = [];

			for(var i = 0; i < _trxn_list.length; i++) {
				if(_trxn_list[i].checked) {
					_trxn_flow.push(_trxn_list[i].value);
				}
			}

			if (_trxn_flow.length === 0)
			{
				_trxn_list[0].checked = true;
				_trxn_flow.push(_trxn_list[0].value);
			}

			var trxn = _trxn_flow.join(',');

			// var trxn = document.querySelector('#selTrxn').value;

			document.querySelector('#ekowidget').setAttribute("transaction-flow", trxn);
			document.querySelector('#srcTrxnName').innerHTML = trxn;
		}

		function _selectTheme()
		{
			var thm_code = document.querySelector('#selTheme').value;
			var thm;

			if (thm_code == "0")
			{
				thm = {
					dark: _cust_dark_base_theme,
					colors: '{"--accent-color":"' + _cust_colors['--accent-color'] + '", "--card-background-color":"' + _cust_colors['--card-background-color'] + '"}'
				}

				document.querySelector("#custcolorbox").hidden = false;
			}
			else
			{
				thm = _themes[thm_code];

				document.querySelector("#custcolorbox").hidden = true;
			}

			console.log("THEME::: ", thm_code, thm);

			if (thm.dark)
			{
				document.querySelector('#ekowidget').setAttribute("dark-theme", "dark-theme");
				document.querySelector('#srcDarkTheme').hidden = false;
			}
			else
			{
				document.querySelector('#ekowidget').removeAttribute("dark-theme");
				document.querySelector('#srcDarkTheme').hidden = true;
			}

			document.querySelector('#ekowidget').setAttribute("theme-colors", thm.colors || "");
			document.querySelector('#srcThemeColors').innerHTML = (thm.colors || "");
			document.querySelector('#srcThemeColorsBox').hidden = (thm.colors ? false : true);
		}

		function _selectLang()
		{
			var lang = document.querySelector('#selLang').value;

			console.log("LANG::: ", lang);

			document.querySelector('#ekowidget').setAttribute("language", lang || "");
			document.querySelector('#srcLang').innerHTML = (lang || "");
			document.querySelector('#srcLangBox').hidden = (lang === "" || lang === "en" ? true : false);
		}

		function _selectPrint()
		{
			if (document.querySelector('#selPrint').value === "1")
			{
				// Show Print Receipt Option
				document.querySelector('#ekowidget').setAttribute("enable-print-receipt", "enable-print-receipt");
				document.querySelector('#srcReceipt').hidden = false;
			}
			else
			{
				document.querySelector('#ekowidget').removeAttribute("enable-print-receipt");
				document.querySelector('#srcReceipt').hidden = true;
			}
		}


		// Show "debug" window in the widget by adding "debug" attribute
		function _onDebugChange()
		{
			if (document.querySelector('#chkDebug').checked)
			{
				document.querySelector('#ekowidget').setAttribute('debug', 'debug');
				document.querySelector('#srcDebug').hidden = false;
			}
			else
			{
				document.querySelector('#ekowidget').removeAttribute('debug');
				document.querySelector('#srcDebug').hidden = true;
			}
		}


		// Use the dark theme
		function _onShowCodeChange()
		{
			if (document.querySelector('#chkCode').checked)
			{
				document.querySelector('#source').hidden = false;
			}
			else
			{
				document.querySelector('#source').hidden = true;
			}
		}


		// Reset Widget - Goto first transaction card
		function _resetWidget()
		{
			document.querySelector('#ekowidget').resetWidget();
		}



		function _genSecretHash(key, payload)
		{
			encodedKey = btoa(key);

			hmac = new sjcl.misc.hmac(sjcl.codec.utf8String.toBits(encodedKey), sjcl.hash.sha256);
			signature = sjcl.codec.base64.fromBits(hmac.encrypt(""+payload));

			return signature;
		}


		function _colorChanged(part, color)
		{
			console.log("COLOR CHANGED::: ", part, color);
			_cust_colors[part] = color;

			_selectTheme();
		}

		function _darkThemeChanged(dark)
		{
			console.log("DARK THEME CHANGED::: ", dark.checked);
			_cust_dark_base_theme = dark.checked;

			_selectTheme();
		}


		function _ready()
		{
			_selectTransaction();
			_selectTheme();
		}

	</script>


	<script src="https://crypto.stanford.edu/sjcl/sjcl.js"></script>
	</body>
</html>
