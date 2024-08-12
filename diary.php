<?php
$a = $_POST;
if (isset($a['submit'])) {
    $y = $a["year"];

    if ($a['submit'] == "Generate w/ Template") {
        $f = fopen("data.json", "r");
        $data = fread($f, filesize("data.json"));
        fclose($f);

        $a = json_decode($data, true);

        $a['year'] = $y;
        $_POST = $a;
    }
    if ($a['submit'] == "Generate (and save as template)") {
        $data = json_encode($a);
        $f = fopen("data.json", "w");
        fwrite($f, $data);
        fclose($f);
    }



    $months = array(
        1 => "January",
        2 => "February",
        3 => "March",
        4 => "April",
        5 => "May",
        6 => "June",
        7 => "July",
        8 => "August",
        9 => "September",
        10 => "October",
        11 => "November",
        12 => "December"
    );
    $id = 1;
    $people = array();
    while (true) {
        if (isset($_POST["person$id"])) {
            if ($_POST["person$id"] != "") {
                array_push($people, $_POST["person$id"]);
            }
        } else {
            break;
        }
        $id++;
    }
    //$people = array("Dave", "Jules", "Cerian", "Archey");
    $birthdayCol = $a['birthdayCol'] == "yes";
    $birthdays = array();
    $id = 1;
    if ($birthdayCol) {
        while (true) {

            if (isset($_POST["birthperson$id"])) {
                if ($_POST["birthperson$id"] != "" && $_POST["birthdate$id"] != "") {
                    $birthdays[$_POST["birthperson$id"]] = $_POST["birthdate$id"];
                }
            } else {
                break;
            }
            $id++;
        }

        $calendar = "";
        $tmp = array();
        foreach ($birthdays as $key => $value) {

            $date = strtotime($value);
            $date = date("d-m-$y", $date);
            $tmp[$key]  = $date;
        }
        $birthdays = $tmp;
    }
    $fonts = array(
        "Arial",
        "monospace",
        "Comic Sans MS",
        "Brush Script Std, Brush Script MT, cursive",
        "Impact, fantasy"
    );
    foreach ($months as $no => $month) {
        $day = 1;

        print("<div style=\" page-break-after: always; width: 21cm; height: 26cm;\"><span style=\"font-family: " . $fonts[rand(0, 4)] . "; width: 100%; text-align: center;\"><h2>$month $y</h2></span><table class=\"diaryTbl\" style=\"width: 100%; height: 24.5cm\">");
        print("<thead><tr><th style=\"width: 40px;\">Date</th>");
        $peopleRow = "";
        foreach ($people as $person) {
            $peopleRow .= "<td></td>";
            print("<th>$person</th>");
        }
        if ($birthdayCol) {
            print("<th style=\"width: 70px;\">Birthdays</th>");
        }
        print("</tr></thead>");
        $days = cal_days_in_month(CAL_GREGORIAN, $no, $y);
        while ($day <= $days) {
            if ($birthdayCol) {
                $names = "";
                foreach ($birthdays as $name => $date) {
                    if (strtotime($date) == strtotime("$day-$no-$y")) {
                        $names .= "$name ";
                    }
                }
                $birthday = "<td>$names</td>";
            } else {
                $birthday = "";
            }
            $date = (date("D j", strtotime("$day-$no-$y")) . " <br>");
            print("<tr><td><b>$date</b></td>$peopleRow$birthday</tr>");
            $day++;
        }
        print("</table><center><b>$month $y</b></center></div>");
    }
    $printCal = True;
}
?>

<html>

<head>
    <meta charset="UTF-8">
    <title>Calendar Generator</title>
    <meta name="viewport" content="width=500px, initial-scale=0.8">
    <style>
        body {
            size: 21cm 29.7cm;
            margin: 30mm 45mm 30mm 45mm;
            /* change the margins as you want them to be. */
        }

        .diaryTbl {
            border-collapse: collapse;
        }

        th {
            width: 100px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        td {
            padding: 2px;
        }

        .birthdays table {
            border: none;
            width: 300px;
        }

        .birthdays td {
            border: none;
        }

        h2 {
            margin: 2px;
        }

        button {
            padding: 5px;
            padding-left: 10px;
            padding-right: 10px;
            margin-top: 3px;
        }

        input {
            padding: 5px;
        }
    </style>
</head>

<body>
    <?php
    if (isset($printCal)) {
        if ($printCal) {
            print($calendar);
            exit;
        }
    }
    ?>
    <h3>Calendar Generator</h3>
    <form method="POST">
        <b>Year</b>
        <select name="year">
            <?php
            $year = date("Y");
            $years = [$year - 2, $year - 1, $year, $year + 1, $year + 2];
            foreach ($years as $option) {
                print("<option>$option</option>");
            }
            ?>
        </select><br><br>
        <div id="names">
            Name<br>
            <input type="text" name="person1" id="person1" width="200" style="width: 200px;" />
        </div>
        <button type="button" onclick="addName()">Add</button><br><br>
        Add Birthday Column
        <input type="checkbox" value="yes" name="birthdayCol" checked id="birthdayCol" onclick="if(this.checked) { document.getElementById('birthdays').style.display = 'block'; } else { document.getElementById('birthdays').style.display = 'none'; }" />
        <div id="birthdays" class="birthdays">
            <table>
                <tr>
                    <td>Name<br><input type="text" name="birthperson1" id="birthperson1" style="width: 200px;" /></td>
                    <td>Date<br><input name="birthdate1" id="birthdate1" type="date"></td>
                </tr>
            </table>
        </div>
        <button type="button" onclick="addBirthdate()">Add</button>
        <script type="text/javascript">
            nid = 1;
            bid = 1

            function addName() {
                var values = []
                index = 1
                while (index <= nid) {
                    values.push(document.getElementById("person" + index).value);
                    index++;
                }
                nid++;
                document.getElementById("names").innerHTML += "<br>Name<br><input type=\"text\" name=\"person" + nid + "\" id=\"person" + nid + "\" style=\"width: 200px\"/>";
                var arrayLength = values.length;
                for (var i = 0; i < arrayLength; i++) {
                    a = i + 1

                    document.getElementById("person" + a.toFixed(0)).value = values[i];
                }
            }

            function addBirthdate() {
                var names = []
                var dates = []
                index = 1
                while (index <= bid) {
                    names.push(document.getElementById("birthperson" + index.toFixed(0)).value);
                    dates.push(document.getElementById("birthdate" + index.toFixed(0)).value);
                    index++;
                }
                bid++;
                document.getElementById("birthdays").innerHTML += "<table><tr><td>Name<br><input type=\"text\" name=\"birthperson" + bid + "\" id=\"birthperson" + bid + "\" style=\"width: 200px;\"/></td><td>Date<br><input name=\"birthdate" + bid + "\" id=\"birthdate" + bid + "\" type=\"date\"></td></tr></table>";
                var arrayLength = names.length;
                for (var i = 0; i < arrayLength; i++) {
                    a = i + 1

                    document.getElementById("birthperson" + a.toFixed(0)).value = names[i];
                    document.getElementById("birthdate" + a.toFixed(0)).value = dates[i];
                }
            }
        </script>
        <br>
        <input type="submit" name="submit" value="Generate" />
        <input type="submit" name="submit" value="Generate (and save as template)" />

        <input type="submit" name="submit" value="Generate w/ Template" />
    </form>
    <div style="width: 21 cm; height: 29.7 cm; background: black;">
    </div>
</body>

</html>