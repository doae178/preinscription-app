<!-- HEAD -->
<head>
  <meta charset="UTF-8">
  <title>Formulaire Clavier Arabe</title>

  <!-- Style du clavier -->
  <style>
    .simple-keyboard {
      max-width: 350px;
      margin-top: 10px;
    }
    .input-arabe {
      width: 300px;
      font-size: 18px;
      direction: rtl;
    }
  </style>

  <!-- CDN clavier -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-keyboard@3/build/css/index.css" />
</head>

<!-- BODY -->
<body>
  <label for="nom_arabe">Nom en arabe :</label><br>
  <input id="nom_arabe" class="input-arabe" type="text" placeholder="اكتب هنا"><br>
  <div class="simple-keyboard"></div>

  <!-- JS clavier -->
  <script src="https://cdn.jsdelivr.net/npm/simple-keyboard@3/build/index.js"></script>
  <script>
    const Keyboard = window.SimpleKeyboard.default;

    const myKeyboard = new Keyboard({
      layout: {
        default: ["ض ص ث ق ف غ ع ه خ ح ج د", "ش س ي ب ل ا ت ن م ك ط", "ئ ء ؤ ر لا ى ة و ز ظ", "{space}"],
      },
      theme: "hg-theme-default hg-layout-default myTheme",
      onChange: input => {
        document.querySelector("#nom_arabe").value = input;
      },
      onKeyPress: button => {
        if (button === "{space}") {
          myKeyboard.setInput(myKeyboard.getInput() + " ");
        }
      }
    });

    document.querySelector("#nom_arabe").addEventListener("input", (e) => {
      myKeyboard.setInput(e.target.value);
    });
  </script>
</body>
