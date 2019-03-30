<html>
	<head>
		<script src="https://beta.ekoconnect.in/widget/import-eko-connect-widget.js"></script>
	</head>

	<body>
		<tf-eko-connect-widget
			id = 'ekowidget'
			transaction-flow = 'remittance,remittance_refund'
			developer-key = 'becbbce45f79c6f5109f848acd540567'
			secret-key = 'a6dGwEILHdlezzy1ZycRvKuUr6+6yqAOd7OlL4dvQvc='
			secret-key-timestamp = '1512158087256'
			initiator-id = '9910028267'
			theme-colors='{"--accent-color":"#7E57C2", "--light-accent-color":"#9575CD"}'

			enable-print-receipt
			receipt-logo = 'https://xyz.com/mycompanylogo.png'
			receipt-title = 'ABC Communications'
			receipt-sub-title = 'Shop no.1, New Market, East Delhi - 110001'
			receipt-tnc = 'Terms and conditions appplied for all transactions'

			merchant-document-id-type='1'
            merchant-document-id='AABTG6381M'
            pincode='122003'
            language="en"
            debug>
        </tf-eko-connect-widget>
        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
	</body>
</html>