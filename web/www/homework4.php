<?php
    /**
     * Homework 4 - PHP Introduction
     *
     * Computing ID: cqq7gs
     * Resources used: https://www.w3schools.com/php/php_functions.asp
     */
     
    // Your functions here

    // No closing php tag needed since there is only PHP in this file

    // Problem 1
    function calculateGrade($scores, $drop){
        if(empty($scores)){
            return 0.000;
        }

         if ($drop) {
            $minIndex = 0;
            $minRatio = $scores[0]["score"] / $scores[0]["max_points"]; // ratio of first item

            // Find the index of the lowest ratio
            for ($i = 1; $i < count($scores); $i++) {
                $ratio = $scores[$i]["score"] / $scores[$i]["max_points"];
                if ($ratio < $minRatio) {
                    $minRatio = $ratio;
                    $minIndex = $i;
                }
            }
            // Remove that lowest-percentage project
            array_splice($scores, $minIndex, 1);
        }

        $totalScore = 0;
        $totalMax   = 0;
        foreach ($scores as $proj) {
            $totalScore += $proj["score"];
            $totalMax   += $proj["max_points"];
        }

        if ($totalMax === 0) {
            // Avoid division by zero
            return 0.000;
        }

        $average = ($totalScore / $totalMax) * 100;
        // Round to three decimal places
        return round($average, 3);
    }


    // Problem 2
    function gridCorners($width, $height){
        if ($width <= 0 || $height <= 0) {
            return "";
        }

        if ($width >= 2 && $height >= 2) {
            $cornerTiles = [];
            $corners = [
                [0, 0],                // bottom-left
                [$width - 1, 0],       // bottom-right
                [0, $height - 1],      // top-left
                [$width - 1, $height - 1] // top-right
            ];

            foreach ($corners as $c) {
                list($cx, $cy) = $c; // corner col, corner row

                // Add the corner tile
                $cornerTiles[] = tileNumber($cx, $cy, $height);

                // Add neighbor above (cx, cy+1) if valid
                if ($cy + 1 < $height) {
                    $cornerTiles[] = tileNumber($cx, $cy + 1, $height);
                }
                // Add neighbor below (cx, cy-1) if valid
                if ($cy - 1 >= 0) {
                    $cornerTiles[] = tileNumber($cx, $cy - 1, $height);
                }
                // Add neighbor left (cx-1, cy) if valid
                if ($cx - 1 >= 0) {
                    $cornerTiles[] = tileNumber($cx - 1, $cy, $height);
                }
                // Add neighbor right (cx+1, cy) if valid
                if ($cx + 1 < $width) {
                    $cornerTiles[] = tileNumber($cx + 1, $cy, $height);
                }
            }

            // Remove duplicates, sort them
            $cornerTiles = array_unique($cornerTiles);
            sort($cornerTiles);

            // Convert to comma-separated string
            return implode(", ", $cornerTiles);
        } 
        else {
            $edgeTiles = [];

            // Bottom row
            for ($col = 0; $col < $width; $col++) {
                $edgeTiles[] = tileNumber($col, 0, $height);
            }

            // Top row
            if ($height > 1) {
                for ($col = 0; $col < $width; $col++) {
                    $edgeTiles[] = tileNumber($col, $height - 1, $height);
                }
            }

            // Left column 
            for ($row = 1; $row < $height - 1; $row++) {
                $edgeTiles[] = tileNumber(0, $row, $height);
            }

            // Right column 
            if ($width > 1) {
                for ($row = 1; $row < $height - 1; $row++) {
                    $edgeTiles[] = tileNumber($width - 1, $row, $height);
                }
            }

            // Remove duplicates, sort
            $edgeTiles = array_unique($edgeTiles);
            sort($edgeTiles);

            if (empty($edgeTiles)) {
                return "";
            }

            return implode(", ", $edgeTiles);
        }
    }

    // Helper function to convert (col, row) -> tile number
    function tileNumber($col, $row, $height) {
        return $col * $height + $row + 1;
    }


    // Problem 3
    function combineShoppingLists(...$lists) {
        $merged = [];

        foreach ($lists as $oneList) {
            if (!isset($oneList["user"]) || !isset($oneList["list"])) {
                continue;
            }
            $userName = $oneList["user"];
            $items    = $oneList["list"];

            foreach ($items as $item) {
                if (!isset($merged[$item])) {
                    $merged[$item] = [];
                }
                // We might check if $userName is already there, but typically it's not repeated
                if (!in_array($userName, $merged[$item])) {
                    $merged[$item][] = $userName;
                }
            }
        }

        // Sort by alphabetical order of the item
        ksort($merged);

        return $merged;
    }



    //Problem 4
    function acronymSummary($acronyms, $searchString) {
        if (!is_string($acronyms) || !is_string($searchString) ||
            trim($acronyms) === "" || trim($searchString) === "") {
            return [];
        }

        // Split the acronyms by whitespace
        $acroArr = preg_split('/\s+/', trim($acronyms));

        preg_match_all('/[A-Za-z]+/', $searchString, $matches);
        $words = $matches[0]; 

        // Convert them to lower for matching
        $lowerWords = array_map('strtolower', $words);

        $summary = [];

        foreach ($acroArr as $acro) {
            $summary[$acro] = 0; 
            $acroLower = strtolower($acro);
            $n = strlen($acroLower);

            // Slide over $lowerWords with a window of size n
            $maxIndex = count($lowerWords) - $n;
            for ($i = 0; $i <= $maxIndex; $i++) {
                $match = true;
                for ($j = 0; $j < $n; $j++) {
                    // first letter of each word
                    if ($lowerWords[$i + $j][0] !== $acroLower[$j]) {
                        $match = false;
                        break;
                    }
                }

                if ($match) {
                    $summary[$acro]++;
                }
            }
        }

        return $summary;
    }