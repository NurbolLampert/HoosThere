<?php
    include("homework4.php");

    // Hint: include error printing!
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
?><!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">  
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="your name">
  <meta name="description" content="include some description about your page">  
    
  <title>Homework 4 Test File</title>
</head>
<body>
<h1>Homework 4 Test File</h1>

<h2>Problem 1: calculateGrade Tests</h2>
<?php
    // 1. Basic test with two identical scores, no drop
    $test1 = [
        [ "score" => 55, "max_points" => 100 ],
        [ "score" => 55, "max_points" => 100 ]
    ];
    echo "<p><strong>Test 1:</strong> " . calculateGrade($test1, false) . 
         " (Expected ~ 55.000)</p>";

    // 2. Same test, but drop the lowest score
    //    Both have the same percentage, so dropping either yields the same result
    echo "<p><strong>Test 2:</strong> " . calculateGrade($test1, true) . 
         " (Expected ~ 55.000, since only one project remains)</p>";

    // 3. Different percentages, no drop
    $test2 = [
        [ "score" => 70, "max_points" => 100 ], // 70%
        [ "score" => 8,  "max_points" => 10  ], // 80%
        [ "score" => 90, "max_points" => 100 ]  // 90%
    ];
    // average = (70 + 8 + 90) / (100 + 10 + 100) * 100 = 168 / 210 * 100 = 80.0
    echo "<p><strong>Test 3:</strong> " . calculateGrade($test2, false) . 
         " (Expected 80.000)</p>";

    // 4. Different percentages, drop the lowest
    //    The lowest is 70/100 = 70%
    //    So we drop the 70% and keep 80% + 90% => new average = (8+90)/(10+100)*100 = 98/110*100 = 89.09
    echo "<p><strong>Test 4:</strong> " . calculateGrade($test2, true) . 
         " (Expected ~ 89.091)</p>";

    // 5. Edge case: empty array
    echo "<p><strong>Test 5:</strong> " . calculateGrade([], false) . 
         " (Expected 0.000, no scores)</p>";

    // 6. Edge case: total max_points = 0?
    $test3 = [
        [ "score" => 0, "max_points" => 0 ]
    ];
    echo "<p><strong>Test 6:</strong> " . calculateGrade($test3, false) . 
         " (Expected 0.000, avoid divide-by-zero)</p>";
?>

<h2>Problem 2: gridCorners Tests</h2>
<?php
    // 1. Standard example from the homework prompt: width=3, height=4
    echo "<p><strong>Test 1 (3x4):</strong> " . gridCorners(3, 4) . 
         " (Expected: 1, 2, 3, 4, 5, 8, 9, 10, 11, 12)</p>";

    // 2. Width=2, Height=2 (minimum for the corner brackets)
    //    2 wide x 2 tall => 4 tiles total:
    //    3 4
    //    1 2
    //    Each corner bracket basically covers the entire grid
    echo "<p><strong>Test 2 (2x2):</strong> " . gridCorners(2, 2) . 
         " (Should be all 4 tiles: 1, 2, 3, 4)</p>";

    // 3. Very wide but short (width=5, height=1)
    //    If height=1, we can't have a bracket of 2 in height, so we do all edges
    echo "<p><strong>Test 3 (5x1):</strong> " . gridCorners(5, 1) . 
         " (Expected: 1, 2, 3, 4, 5, i.e. the entire row)</p>";

    // 4. Very tall but narrow (width=1, height=5)
    //    If width=1, we can't have a bracket of 2 in width, so we do all edges
    echo "<p><strong>Test 4 (1x5):</strong> " . gridCorners(1, 5) . 
         " (Expected: 1, 2, 3, 4, 5)</p>";

    // 5. Zero dimension
    echo "<p><strong>Test 5 (0x3):</strong> " . gridCorners(0, 3) . 
         " (Expected an empty string)</p>";

    // 6. Another test: width=3, height=2
    //    1 3 5
    //    2 4 6
    //    With corners, we have bottom-left (1) bracket => 1,2,4
    //    bottom-right(5) => 5,4,6
    //    top-left(2) => 2,1,3
    //    top-right(6) => 6,5,3
    //    Combine unique => 1,2,3,4,5,6 (which is everything)
    echo "<p><strong>Test 6 (3x2):</strong> " . gridCorners(3, 2) . 
         " (Expected: 1, 2, 3, 4, 5, 6)</p>";
?>

<h2>Problem 3: combineShoppingLists Tests</h2>
<?php
    // 1. Basic example from homework
    $list1 = [
        "user" => "Fred",
        "list" => ["frozen pizza", "bread", "apples", "oranges"]
    ];
    $list2 = [
        "user" => "Wilma",
        "list" => ["bread", "apples", "coffee"]
    ];
    $combined = combineShoppingLists($list1, $list2);

    echo "<p><strong>Test 1:</strong><br>";
    echo "Input lists: <br>";
    echo "<pre>";
    print_r([$list1, $list2]);
    echo "</pre>";
    echo "Merged Output: <br>";
    echo "<pre>";
    print_r($combined);
    echo "</pre>";
    echo "Expected keys (alphabetically): apples, bread, coffee, frozen pizza, oranges.<br>";
    echo "  - apples => [\"Fred\", \"Wilma\"]<br>";
    echo "  - bread => [\"Fred\", \"Wilma\"]<br>";
    echo "  - coffee => [\"Wilma\"]<br>";
    echo "  - frozen pizza => [\"Fred\"]<br>";
    echo "  - oranges => [\"Fred\"]</p>";

    // 2. Edge case: single list
    $list3 = [
        "user" => "Barney",
        "list" => ["milk", "eggs", "juice"]
    ];
    $combined2 = combineShoppingLists($list3);
    echo "<p><strong>Test 2 (Single list):</strong><br>";
    echo "<pre>";
    print_r($combined2);
    echo "</pre>";
    echo "Expected: [\"eggs\"=>[\"Barney\"], \"juice\"=>[\"Barney\"], \"milk\"=>[\"Barney\"]]</p>";

    // 3. Malformed or missing data
    $listBad = [
        "usr" => "BadUser",
        "lst" => ["something"] // keys do not match "user" and "list"
    ];
    $testCombined = combineShoppingLists($list1, $listBad);
    echo "<p><strong>Test 3 (Malformed data):</strong><br>";
    echo "<pre>";
    print_r($testCombined);
    echo "</pre>";
    echo "Expected: Only items from the valid Fred list. (No errors, just no items from the bad list)</p>";

    // 4. Overlapping users
    $list4 = [
        "user" => "Fred",
        "list" => ["milk", "cereal"]
    ];
    $merged2 = combineShoppingLists($list3, $list4); 
    echo "<p><strong>Test 4 (User overlap):</strong><br>";
    echo "Input: <br>";
    echo "<pre>";
    print_r([$list3, $list4]);
    echo "</pre>";
    echo "Merged Output: <br>";
    echo "<pre>";
    print_r($merged2);
    echo "</pre>";
    echo "Check that 'milk' => [\"Barney\", \"Fred\"].</p>";
?>

<h2>Problem 4: acronymSummary Tests</h2>
<?php
    // 1. Example from homework
    $acronyms = "rofl lol afk";
    $searchString = "Rabbits on freezing lakes only like really old fleece leggings.";
    $acrosum = acronymSummary($acronyms, $searchString);

    echo "<p><strong>Test 1:</strong><br>";
    echo "Acronyms: $acronyms <br>";
    echo "Search String: $searchString <br>";
    echo "Result:<br><pre>";
    print_r($acrosum);
    echo "</pre>";
    echo "Expected: [\"rofl\"=>2, \"lol\"=>1, \"afk\"=>0]</p>";

    // 2. Case-insensitivity check
    $acronyms2 = "ABC";
    $searchString2 = "Always Buy Carrots. Another Big Cat. Another Blazing Comet.";
    // We can find "ABC" in
    //   "Always Buy Carrots." => A, B, C
    //   "Another Big Cat." => A, B, C
    //   "Another Blazing Comet." => A, B, C
    // Each set of 3 consecutive words starts with A, B, C ignoring case => total 3
    $acrosum2 = acronymSummary($acronyms2, $searchString2);
    echo "<p><strong>Test 2 (Case-insensitivity):</strong><br>";
    echo "Acronym: $acronyms2 <br>";
    echo "Search String: $searchString2 <br>";
    echo "<pre>";
    print_r($acrosum2);
    echo "</pre>";
    echo "Expected: [\"ABC\"=>3]</p>";

    // 3. No matches at all
    $acronyms3 = "XYZ LMAO";
    $searchString3 = "No matching word groups here.";
    $acrosum3 = acronymSummary($acronyms3, $searchString3);
    echo "<p><strong>Test 3 (No matches):</strong><br>";
    echo "Acronyms: $acronyms3 <br>";
    echo "Search String: $searchString3 <br>";
    echo "<pre>";
    print_r($acrosum3);
    echo "</pre>";
    echo "Expected: [\"XYZ\"=>0, \"LMAO\"=>0]</p>";

    // 4. Empty or invalid parameters
    echo "<p><strong>Test 4 (Empty string):</strong><br>";
    echo "Expected empty array: <pre>";
    print_r(acronymSummary("", ""));
    echo "</pre></p>";
?>

<p>...</p>
</body>
</html>
