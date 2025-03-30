// function toggleFlip(card) {
//     card.classList.toggle('flipped');
//   }  

// function googleTranslateElementInit() {
      //   new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
      // }
    
      function googleTranslateElementInit() {
        new google.translate.TranslateElement({
          pageLanguage: 'en',
          layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
          "includedLanguages": "vi,zh-CN,hi,es,fr,ar,ru,pt,ja,ko,id,ms,th,my,km,lo,tl,de,it,fa,ur,pl,tr,uk,ro,nl,sv,cs,he,el,hu,da,fi",
          autoDisplay: false
        }, 'google_translate_element');
      }