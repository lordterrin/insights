<?php
?>

<?php include 'header.php';?>
<?php include 'footer.php';?>

<body>

<div class="header">

  <div class="top_header">
    <div class="left_header">
      <div class="left_header_title"><img class="ensights" src="img/ensights.png" /></div>
      <div class="left_header_subtitle">Insights for Ensign Leaders</div>
    </div>
    <div class="right_header">
      <div class="insight_quote"></div>
    </div>
  </div>

  <div class="subheader">
    <div id="insight_one" class="right_header_item insight--deselected">
      <div class="header_image"><img id="wave1" src="img/wave.png"></div>
      <div class="header_subtitle">Insight 1</div>
    </div>
    <div id="insight_two" class="right_header_item insight--deselected">
      <div class="header_image"><img id="wave2" src="img/wave.png"></div>
      <div class="header_subtitle">Insight 2</div>
    </div>
    <div id="insight_three" class="right_header_item insight--deselected">
      <div class="header_image"><img id="wave3" src="img/wave.png"></div>
      <div class="header_subtitle">Insight 3</div>
    </div>
  </div>
</div>

<div class="insight_body">
  <canvas class="teh_chart" id="myChart" width="400" height="400"></canvas>
  <div class="insight_three_notes"></div>
</div>

<div class="insight_footer">

    <div class="th_footer_filter hideme">
      <div title="search by literally any word anywhere" id="units_table_filter_alt" class="dataTables_filter_alt">
        <label>
          <div class="filter_text_alt">Filter</div>
          <input id="th_filter" type="search">
        </label>
      </div>
    </div>

    <div class="footer_item"></div>
    <div class="footer_item" onclick="about_swal()">About</div>
    <div class="footer_item">Home</div>
  </div>


</body>

<script>

var ctx = document.getElementById('myChart');

$(document).on('click', '#insight_one', function() {

  quote = "...provides information submitted by nursing homes including rehabilitation services on a quarterly basis...";
  url   = "https://data.cms.gov/data-api/v1/dataset/73f63730-8230-41f8-a33f-b740c7e79dde/data?column=PROVNUM%2CWorkDate%2CHrs_RN%2CSTATE&offset=0&size=1000&filter[STATE]=AZ";
  iid   = 1;

  process_insight(quote, url, iid);

});

$(document).on('click', '#insight_two', function() {

  quote = "...provides information related to quality and performance measures for groups of nursing homes...";
  url   = "https://data.cms.gov/data-api/v1/dataset/97ecfad1-d3f1-4d42-b774-d74661d830bc/data";
  iid   = 2;

  process_insight(quote, url, iid);

});

$(document).on('click', '#insight_three', function() {

  quote = "...provides information submitted by nursing homes including rehabilitation services on a quarterly basis...";
  url   = "https://data.cms.gov/data-api/v1/dataset/97ecfad1-d3f1-4d42-b774-d74661d830bc/data";
  iid   = 3;

  process_insight(quote, url, iid);

});

function process_insight(quote, url, iid) {

  $("#wave" + iid).addClass('rotate');

  $(".insight_quote").text(quote);

  $.ajax({
    type: "GET",
    url: url,
    success: function(response) {

      $("#wave" + iid).removeClass('rotate');

      insight_data = response;

      if(myChart){
        try {
          myChart.clear();
          myChart.destroy();
        }catch{
          console.log("no chart");
        }
      }

      if ( iid == 1 ) {
        process_insight_one();
      } else if ( iid == 2 ) {
        process_insight_two();
      } else if ( iid == 3 ) {
        process_insight_three();
      }

    },
    error: function(xhr, ajaxOptions, thrownError) {
      console.log(xhr.responseText);
    }
  });
}

function process_insight_one() {

  /* this function AVERAGES out data pulled from data.cms.gov into one of two categories:
    - data where the PROVNUM is part of Ensign
    - data where the PROVNUM is not part of Ensign
  */

  $('#insight_one, #insight_two, #insight_three').addClass('insight--deselected');
  $('#insight_one').removeClass('insight--deselected');

  let a = insight_data;
  let e_count = 0;
  let o_count = 0;
  e = [];
  o = [];
  insight_one_e = [];
  insight_one_o = [];

  for ( var p=0; p<a.length; p++ ) {

    let provnum = a[p].PROVNUM; /* either an Ensign location or not - data is held in var insight_ids */
    let year    = a[p].WorkDate.substring(0,4); /* 20231009 --> 2023 */
    let month   = a[p].WorkDate.substring(4,6); /* 20231009 --> 10 */
    let day     = a[p].WorkDate.substring(6,8); /* 20231009 --> 09 */

    if (Object.values(insight_ids).indexOf(provnum) > -1) {
       x = {
        total_rn_hours : a[p].Hrs_RN,
        year           : year,
        month          : month,
        //day            : day,
       }

       e.push(x);

    } else {

      x = {
        total_rn_hours : a[p].Hrs_RN,
        year           : year,
        month          : month,
        //day            : day,
       }

       o.push(x);
    }
  }

  april = 0;
  may = 0;
  june = 0;

  for ( var x=0; x<e.length; x++ ) {

    if ( e[x].month == "04" ) {
      april = april + Number(e[x].total_rn_hours);
    } else if ( e[x].month == "05" ) {
      may = may + Number(e[x].total_rn_hours);
    } else if ( e[x].month == "06" ) {
      june = june + Number(e[x].total_rn_hours);
    }

  }

  y = {
    group       : 'Ensign',
    rn_hours    : Math.floor(april),
    year        : 2023,
    month       : 4,
  }

  insight_one_e.push(y);

  y = {
    group       : 'Ensign',
    rn_hours    : Math.floor(may),
    year        : 2023,
    month       : 5,
  }

  insight_one_e.push(y);

  y = {
    group       : 'Ensign',
    rn_hours    : Math.floor(june),
    year        : 2023,
    month       : 6,
  }

  insight_one_e.push(y);

  april = 0;
  may = 0;
  june = 0;
  for ( var x=0; x<o.length; x++ ) {

    if ( o[x].month == "04" ) {
      april = april + Number(o[x].total_rn_hours);
    } else if ( o[x].month == "05" ) {
      may = may + Number(o[x].total_rn_hours);
    } else if ( o[x].month == "06" ) {
      june = june + Number(o[x].total_rn_hours);
    }

  }

  y = {
    group       : 'Everyone Else',
    rn_hours    : Math.floor(april),
    year        : 2023,
    month       : 4,
  }

  insight_one_o.push(y);

  y = {
    group       : 'Everyone Else',
    rn_hours    : Math.floor(may),
    year        : 2023,
    month       : 5,
  }

  insight_one_o.push(y);

  y = {
    group       : 'Everyone Else',
    rn_hours    : Math.floor(june),
    year        : 2023,
    month       : 6,
  }

  insight_one_o.push(y);


  myChart = new Chart(ctx, {
  type: 'line',
  data: {
      labels: ['April', 'May','June'],
      datasets: [
        {
          label: 'Ensign Total RN Hours Billed',
          data: insight_one_e.map(row=>row.rn_hours),
          borderColor :
          [
            getRandomColor()
          ],
          borderWidth : 4,
          tension : 0.3
        },
        {
          label: 'Everyone Else Total RN Hours Billed',
          data: insight_one_o.map(row=>row.rn_hours),
          borderColor :
          [
            getRandomColor()
          ],
          borderWidth : 4,
          tension : 0.3
        },
      ],

  },
  options: {
      maintainAspectRatio : false,
      responsive: true,
      elements : {
        bar : {
          borderWidth : 4,
        }
      },
    plugins: {
      title: {
        display: true,
        text: 'Payroll Based Journal Daily Nurse Staffing : Q3 2023'
      }
    }
  }
});

  $('.teh_chart').removeClass("hideme");
  $(".insight_three_notes").addClass("hideme");

}

function process_insight_two() {

  /* this function AVERAGES out data pulled from data.cms.gov into one of two categories:
    - data where the PROVNUM is part of Ensign
    - data where the PROVNUM is not part of Ensign
  */

  $('#insight_one, #insight_two, #insight_three').addClass('insight--deselected');
  $('#insight_two').removeClass('insight--deselected');

  let a = insight_data;
  insight_two = [];
  let other_count = 0;
  let other_fines_count = 0;
  let other_fines_amount = 0;
  let other_rating = 0;
  let other_staff = 0;

  for ( var p=0; p<a.length; p++ ) {

    let id            = a[p]["Affiliated entity ID"];
    let fines_count   = a[p]["Average number of fines"];
    let fines_amount  = a[p]["Average amount of fines in dollars"];
    let rating        = a[p]["Average quality rating"];
    let staffing      = a[p]["Average staffing rating"];

    /* isolate Ensign group */
    if (id == 507) {
      x = {
        group         : 'Ensign Group',
        fines_count   : Number(fines_count),
        fines_amount  : Math.floor(fines_amount),
        rating        : Number(rating),
        staffing      : Number(staffing),
      }

      insight_two.push(x);

      for ( var x=0; x<insight_data.length; x++) {
        if (insight_data[x]["Affiliated entity ID"] == 507 ) {
          console.log(x);
        }
      }

    } else if (id == "" ) {

      /* skip - this is the "national" value that skews everything */

    } else {
      /* add all other items together, then get the average */
      other_count = other_count + 1;
      other_fines_count = other_fines_count + Number(a[p]["Average number of fines"]);
      other_fines_amount = other_fines_amount + (Number(a[p]["Average amount of fines in dollars"]));
      other_rating = other_rating + Number(a[p]["Average quality rating"]);
      other_staff = other_staff + Number(a[p]["Average staffing rating"]);
    }

  }

  other_fines_count = +(other_fines_count / other_count).toFixed(2);
  other_fines_amount = +(other_fines_amount / other_count).toFixed(2);
  other_rating = +(other_rating / other_count).toFixed(2);
  other_staff = +(other_staff / other_count).toFixed(2);

  x = {
    group           : 'Everyone Else',
    fines_count     : other_fines_count,
    fines_amount    : other_fines_amount,
    rating          : other_rating,
    staffing        : other_staff,
  }

  insight_two.push(x);


myChart = new Chart(ctx, {
  type: 'bar',
  data: {
      labels: insight_two.map(row=>row.group),
      datasets: [
        {
          label: 'Average Rating per quarter',
          data: insight_two.map(row=>row.rating),
          backgroundColor :
          [
            getRandomColor(),
            getRandomColor(),
          ],
        },
        {
          label: 'Avg. Fine Amount per quarter',
          hidden: true,
          data: insight_two.map(row=>row.fines_amount),
          backgroundColor :
          [
            getRandomColor(),
            getRandomColor(),
          ],
        },
        {
          label: 'Avg. Number of Fines per quarter',
          hidden: true,
          data: insight_two.map(row=>row.fines_count),
          backgroundColor :
          [
            getRandomColor(),
            getRandomColor(),
          ],
        },
        {
          label: 'Avg. Staff Rating',
          hidden: true,
          data: insight_two.map(row=>row.staffing),
          backgroundColor :
          [
            getRandomColor(),
            getRandomColor(),
          ],
        }
      ],

  },
  options: {
      indexAxis : 'y',
      maintainAspectRatio : false,
      elements : {
        bar : {
          borderWidth : 2,
        }
      },
      responsive: true,
    plugins: {
      legend: {
        position: 'right',
      },
      title: {
        display: true,
        text: 'Nursing Home Affiliated Entity Performance Measures : Q3 2023'
      }
    }
  }
});

  $('.teh_chart').removeClass("hideme");
  $(".insight_three_notes").addClass("hideme");
}

function process_insight_three() {

  $('#insight_one, #insight_two, #insight_three').addClass('insight--deselected');
  $('#insight_three').removeClass('insight--deselected');
  $(".insight_quote").text("...my thoughts on all of this...");

  insight_three = `

    <h2>Honest Thoughts</h2>
    <h3>Regarding the prompt</h3>
    <ul>
      <li>The goal set forth by the leadership team is for us to help provide insights that allow them to improve/optimize their Nursing Staffing in meaningful ways</li>

      <li>The data available through data.cms.gov is amazing, vast, and incredibly pertinant to directors and administrators of nursing facilities.  My intent here was to showcase a few very simple ways that data <i>could</i> be pulled via live API and then analyzed. For both of my "Insights", I brought in raw data, grouped the data into Ensign vs. everyone else arrays, and then displayed them to the user using chart.js.</li>
      <li>What is possible using the charting libraries I've chosen is incredible given enough time, but probably not on par with paid software like Tableau or Power BI, <i>(which I would choose for something like this)</i>.</li>
      <li>While an API that connects into this data source can be useful for some of the smaller datasets <i>(e.g. Insight 2 with 600 rows)</i>, for this data to be useful long-term it should be storied locally in a big data environment - or at a minimum - scraped from the web and stored in a SQL database that we could query locally, as this would allow for much deeper levels of analytics that were not built here <i>(e.g. Insight 1 with 1m+ rows)</i>.  A live API feed that would require being hit multiple times for a single query is just too slow, and the API itself has very limited capabilities outside of returning raw data.</li>
      <li>Given the constraints of this example, it is difficult for me to provide <i>meaningful</i> information that would allow any real improvements or optimizations to occur.</li>
      <li>In a real business context, there would be <b>far</b> more options to allow users to:
      <ul>
        <li>query (grouping, filtering, SQL-esque type choices</li>
        <li>format (which fields do <b>you</b> want to see?)</li>
        <li>utilize their own unique time frame instead of forcing the display of Q3 2023, or provide the ability to add in multiple time frames</li>
      </ul>
    </ul>

    <h3>Regarding the data</h3>
    <ul>
      <li>Without understanding the ins and outs of the process, it appears that on average, Ensign's facilities acheive a higher rating than all of our competitors put together - and by quite a decent margin in certain cases. </li>
      <li>As a potential employee, this warms my heart.  Ensign appears to walk the walk.</li>
        <ul>
          <li>The <b>overall 5-star rating</b> and <b>average heath inspection rating</b> are best in class</li>
          <li>However, one pain point that lowers the <i>overall</i> rating is the <b>average staffing rating</b>,  which is lower, on average, than other groups' facilities. While there are several facilities that earn a "1/5" rating, we should not be trying to compare ourselves against these, but rather, against the many that receive "5/5".  It is good to know that a "5/5" rating is in fact possible</li>
        </ul>

      <li>For the vast majority of metrics that compare Ensign's facilities with other companies' facilities at a group level, Ensign is doing remarkably well.</li>
        <ul>
          <li>A few spots that leadership should consider looking into might be the <b>average RN hours per resident day</b> and the <b>average RN turnover percentage</b> in our facilities.  Both of these are worse than the national average.</li>
          <li>Understanding why we lose RNs at a higher rate than our competitors is worth looking into, as by all other measures it would appear that our facilities are actually better places to work, which makes these data points interesting</li>
        </ul>
    </ul>

  `;

  $('.teh_chart').addClass("hideme");
  $(".insight_three_notes").removeClass("hideme").html(insight_three);


}

function randomInteger(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

function getRandomColor() {

  r = randomInteger(55,115);
  g = randomInteger(55,215);
  b = randomInteger(55,215);

  return "rgb(" + r + "," + g + "," + b + ")";
}


/* while absolutely a poor design decision, these are hardcoded for now */
insight_ids = [
  "035014",
  "035068",
  "035070",
  "035071",
  "035072",
  "035076",
  "035083",
  "035087",
  "035088",
  "035092",
  "035101",
  "035103",
  "035105",
  "035106",
  "035110",
  "035111",
  "035131",
  "035132",
  "035135",
  "035144",
  "035151",
  "035159",
  "035164",
  "035171",
  "035174",
  "035183",
  "035189",
  "035190",
  "035232",
  "035241",
  "035245",
  "035297",
  "055067",
  "055182",
  "055237",
  "055353",
  "055374",
  "055394",
  "055430",
  "055505",
  "055519",
  "055570",
  "055632",
  "055689",
  "055706",
  "055734",
  "055744",
  "055756",
  "055830",
  "055890",
  "055987",
  "056014",
  "056104",
  "056182",
  "056215",
  "056267",
  "056328",
  "056337",
  "056360",
  "056364",
  "056372",
  "056401",
  "056411",
  "065077",
  "065100",
  "065108",
  "065113",
  "065222",
  "065230",
  "065265",
  "065283",
  "065318",
  "065320",
  "065321",
  "065322",
  "065404",
  "065417",
  "135011",
  "135018",
  "135020",
  "135068",
  "135076",
  "135082",
  "135087",
  "135105",
  "135125",
  "135129",
  "135134",
  "165156",
  "165245",
  "165268",
  "165362",
  "165428",
  "165444",
  "175298",
  "175332",
  "175548",
  "175550",
  "175551",
  "175555",
  "175558",
  "285055",
  "285130",
  "285135",
  "285183",
  "285238",
  "285240",
  "295020",
  "425105",
  "425159",
  "425379",
  "425391",
  "455576",
  "455586",
  "455625",
  "455637",
  "455672",
  "455689",
  "455732",
  "455745",
  "455754",
  "455904",
  "455925",
  "455934",
  "455960",
  "455969",
  "465003",
  "465009",
  "465064",
  "465069",
  "465072",
  "465091",
  "465095",
  "465098",
  "465100",
  "465101",
  "465104",
  "465108",
  "465109",
  "465115",
  "465119",
  "465143",
  "465160",
  "465188",
  "505074",
  "505081",
  "505181",
  "505206",
  "505243",
  "505262",
  "505294",
  "505304",
  "505315",
  "505325",
  "505347",
  "505407",
  "505434",
  "525348",
  "525497",
  "555070",
  "555249",
  "555257",
  "555258",
  "555259",
  "555326",
  "555425",
  "555458",
  "555478",
  "555545",
  "555596",
  "555613",
  "555739",
  "555765",
  "555770",
  "555796",
  "555804",
  "555871",
  "555873",
  "555875",
  "675081",
  "675111",
  "675272",
  "675282",
  "675509",
  "675560",
  "675579",
  "675593",
  "675597",
  "675611",
  "675645",
  "675651",
  "675689",
  "675743",
  "675766",
  "675774",
  "675808",
  "675889",
  "675901",
  "675925",
  "675933",
  "675962",
  "675972",
  "676010",
  "676023",
  "676029",
  "676042",
  "676048",
  "676049",
  "676081",
  "676113",
  "676137",
  "676158",
  "676190",
  "676194",
  "676220",
  "676230",
  "676238",
  "676250",
  "676251",
  "676253",
  "676272",
  "676281",
  "676312",
  "676331",
  "676392",
  "676395",
  "676413",
  "676421",
  "676426",
  "676432",
  "676459"
];



</script>
