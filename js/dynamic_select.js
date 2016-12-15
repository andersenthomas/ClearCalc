function setup(ans) {
  html_string = ''
  if (ans == 'v1') {
    html_string = 'How many legs ? '
    html_string = html_string + '<SELECT NAME="q2" ONCHANGE="alert(document.quest.q2.value)">'
    html_string = html_string + '<OPTION VALUE="">- Please select -</OPTION>'
    html_string = html_string + '<OPTION VALUE="cat">4</OPTION>'
    html_string = html_string + '<OPTION VALUE="sparrow">2</OPTION>'
    html_string = html_string + '<OPTION VALUE="snake">0</OPTION>'
    html_string = html_string + '</SELECT>'
  }
  if (ans == 'v4') {
    html_string = 'What colour ? '
    html_string = html_string + '<SELECT NAME="q2" ONCHANGE="alert(document.quest.q2.value)">'
    html_string = html_string + '<OPTION VALUE="">- Please select -</OPTION>'
    html_string = html_string + '<OPTION VALUE="emerald">green</OPTION>'
    html_string = html_string + '<OPTION VALUE="ruby">red</OPTION>'
    html_string = html_string + '<OPTION VALUE="sapphire">blue</OPTION>'
    html_string = html_string + '</SELECT>'
  }
  document.getElementById('rep').innerHTML=html_string
}
