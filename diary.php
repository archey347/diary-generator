<?php
$a = $_POST;
if (isset($a['submit'])) {
    $y = $a["yea.r"];

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
            /*margin: 30mm 45mm 30mm 45mm;*/
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
        
        <div id="birthdays" class="birthdays" style="padding-top: 10px;">
            <textarea id="data" style="width: 300px; height: 400px; padding-bottom: 10px;"></textarea>
            <table>
                <tr>
                    <td>Name<br><input type="text" name="birth_name" id="birth_name" style="width: 200px;" /></td>
                    <td>Date<br><input name="birth_date" id="birth_date" type="date"></td>
                </tr>
            </table>
            <button type="button" onclick="addBirthdate()">Add</button>
        </div>
        
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

            document.addEventListener("DOMContentLoaded", function(event) { 
                data = localStorage.getItem('birthdays')

                if (data == undefined) {
                    data = JSON.stringify({ birthdays: [] }, null, 2)
                }

                document.getElementById('data').value = data
            });

            function addBirthdate() {
                var data = {}
                
                try {
                    data = JSON.parse(document.getElementById('data').value)
                } catch {
                    alert("Invalid JSON")
                    return
                }

                if (data.birthdays == undefined) {
                    data.birthdays = []
                }

                birth_name = document.getElementById('birth_name').value;
                birth_date = document.getElementById('birth_date').value;

                document.getElementById('birth_name').value = '';
                document.getElementById('birth_date').value = '';

                data.birthdays.push({
                    name: birth_name,
                    date: birth_date
                })

                document.getElementById('data').value = JSON.stringify(data, null, 2)
                localStorage.setItem("birthdays", document.getElementById('data').value)
            }
        </script>
        <br>
        <input type="submit" name="submit" value="Generate" />
    </form>
    <div style="width: 21 cm; height: 29.7 cm; background: black;">
    </div>
</body>

</html>