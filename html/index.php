<html>
        <head>
                <title>AWS Web Demo</title>
		<link rel="stylesheet" href="/css/style.css">

                <?php
                        function readInstanceMetaData($path) {
                                $value = file_get_contents("http://169.254.169.254/latest/meta-data/" . $path);
                                echo $value;
                        }
                ?>

                <?php
                        function getCurrentLocalTime() {
                                date_default_timezone_set('America/New_York');
                                echo date('m/d/Y h:i:sa');
                        }
                ?>
		<?php
			function getRegionsTable() {
				$mysqli = new mysqli("[RDS_DNS_ENDPOINT]", "[RDS_USERNAME]", "[RDS_PASSWORD]", "[RDS_DATABASE_NAME]");
				$arrResults = array();
				$results = $mysqli->query("SELECT * FROM regions;");
				while ($row = $results->fetch_assoc()) {
					$arrResults[] = $row;
				}
				echo json_encode($arrResults);
                                $mysqli->close();
			}
		?>
		<script>
			function getRegionInfo(regionCode, regionDIV) {
				// Get the Region and AZ info via a PHP function.
				var az = "<?php readInstanceMetaData("placement/availability-zone"); ?>";
                                var region = az.slice(0,az.length-1);
				var azValue = az.slice(az.length-1,az.length).toUpperCase();
				console.log(az);
				console.log(regionDIV);
				console.log(azValue);
				// Create an array of the valid Region Codes so we can loop through them.
				// @TODO Read the valid Region Codes from RDS
				var awsRegions = ["us-east-1","us-east-2","us-west-1","us-west-2","ca-central-1","eu-central-1","eu-west-1","eu-west-2","eu-west-3","ap-northeast-1","ap-northeast-2","ap-northeast-3","ap-southeast-1","ap-southeast-2","ap-south-1","sa-east-1","us-gov-west-1","us-gov-east-1","eu-central-2","cn-north-1","cn-northwest-1","ap-west-1","ap-east-1"];
				var regionFound = false;
				// Read the Region info from RDS via a PHP function.
				var regionJSON = JSON.parse('<?php getRegionsTable(); ?>');
                                // Loop through the Region JSON data until we match the selected Region
				for (var i=0; i<regionJSON.length; i++) {
					if (regionJSON[i].code == regionCode) {
						// Current Region matches the user-selected Region, so update the Region Info HTML Table
						var regionTable = "<table><tr><th width=25%>Code</th><th width=50%>Name</th><th width=25%>AZ's</th></tr>";
						regionTable += "<tr><td>" + regionJSON[i].code + "</td><td>" + regionJSON[i].name + "</td><td>" + regionJSON[i].zones + "</td></tr>";
						regionTable += "<table>";
						regionFound = true;
						break;
					}
				}
				// Set the contents of the output DIV to the Region Table Data.
				document.getElementById(regionDIV).innerHTML = regionTable;
				// Clear all of the Region DIVs
				for(var i=0; i<awsRegions.length; i++) {
					if (awsRegions[i] != region) {
						document.getElementById(awsRegions[i]).style.backgroundImage = "none";
						document.getElementById(awsRegions[i]).innerHTML = '';
					}
				}


				document.getElementById(regionCode).style.zIndex = 99;
				if (regionCode == "us-gov-east-1" || regionCode == "eu-central-2" || regionCode == "ap-west-1" || regionCode == "ap-east-1") {
					document.getElementById(regionCode).style.backgroundImage = "url('/images/region-new.png')";
				} else {
					document.getElementById(regionCode).style.backgroundImage = "url('/images/region.png')";
				}
				if (regionCode == region) {
					document.getElementById(regionCode).innerHTML = azValue;
				} else {
					// document.getElementById(regionCode).innerHTML = "&#9881";
					document.getElementById(regionCode).innerHTML = "&#9899";
				}
			}
		</script>
                <script>
                        function getHostName() {
                                var hostname = window.location.hostname;
                                document.getElementById("hostname").innerHTML = hostname;
                        }
                </script>
                <script>
                        function setContent() {
                                var az = "<?php readInstanceMetaData("placement/availability-zone"); ?>";
                                var regionDIV = az.slice(0,az.lastIndexOf("-"));
				// console.log(az);
				var region = az.slice(0,az.length-1);
				var azValue = az.slice(az.length-1,az.length).toUpperCase();
				// console.log(region);
				// console.log(azValue);

                                // region = "us-west";
                                var content = "";

                                content = ' \
                                        <span class="block">Availability Zone:</span> <?php readInstanceMetaData("placement/availability-zone"); ?><br /> \
                                        <span class="block">Server:</span> <?php readInstanceMetaData("hostname"); ?><br /> \
                                        <span class="block">IP Address:</span> <?php readInstanceMetaData("local-ipv4"); ?><br/ > \
                                        ';

                                document.getElementById(regionDIV).innerHTML = content;
				document.getElementById(region).innerHTML = azValue;
                        }
                </script>
                <script>
                        function loadPage() {
                                // getHostName();
                                setContent();
                        }
                </script>

        </head>
	<body onload="loadPage()" background="/images/aws-map.png" style="background-repeat: no-repeat;">
                <div id="body">
                        <img src="/images/gplogo.jpg" alt=GreenPAges Technology Solutions" />
			<div id="us-east-1" class="region" onclick="getRegionInfo('us-east-1','bottom-center')"></div><!-- US East (N. Virginia) -->
			<div id="us-east-2" class="region" onclick="getRegionInfo('us-east-2','bottom-center')"></div><!-- US East (Ohio) -->
			<div id="us-west-1" class="region" onclick="getRegionInfo('us-west-1','bottom-center')"></div><!-- US West (N. California) -->
			<div id="us-west-2" class="region" onclick="getRegionInfo('us-west-2','bottom-center')"></div><!-- US West (Oregon) -->
                        <div id="us-gov-east-1" class="region-new" onclick="getRegionInfo('us-gov-east-1','bottom-center')"></div><!-- GovCloud (US-East) -->
                        <div id="us-gov-west-1" class="region" onclick="getRegionInfo('us-gov-west-1','bottom-center')"></div><!-- GovCloud (US-West) -->
                        <div id="ca-central-1" class="region" onclick="getRegionInfo('ca-central-1','bottom-center')"></div><!-- Canada (Central) -->
                        <div id="eu-central-1" class="region" onclick="getRegionInfo('eu-central-1','bottom-center')"></div><!-- EU (Frankfurt) -->
                        <div id="eu-central-2" class="region-new" onclick="getRegionInfo('eu-central-2','bottom-center')"></div><!-- EU (Sweden) -->
                        <div id="eu-west-1" class="region" onclick="getRegionInfo('eu-west-1','bottom-center')"></div><!-- EU (Ireland) -->
                        <div id="eu-west-2" class="region" onclick="getRegionInfo('eu-west-2','bottom-center')"></div><!-- EU (London) -->
                        <div id="eu-west-3" class="region" onclick="getRegionInfo('eu-west-3','bottom-center')"></div><!-- EU (Paris) -->
                        <div id="ap-northeast-1" class="region" onclick="getRegionInfo('ap-northeast-1','bottom-center')"></div><!-- Asia Pacific (Tokyo) -->
                        <div id="ap-northeast-2" class="region" onclick="getRegionInfo('ap-northeast-2','bottom-center')"></div><!-- Asia Pacific (Seoul) -->
                        <div id="ap-northeast-3" class="region" onclick="getRegionInfo('ap-northeast-3','bottom-center')"></div><!-- Asia Pacific (Osaka-Local) -->
                        <div id="ap-southeast-1" class="region" onclick="getRegionInfo('ap-southeast-1','bottom-center')"></div><!-- Asia Pacific (Singapore) -->
                        <div id="ap-southeast-2" class="region" onclick="getRegionInfo('ap-southeast-2','bottom-center')"></div><!-- Asia Pacific (Sydney) -->
                        <div id="ap-east-1" class="region-new" onclick="getRegionInfo('ap-east-1','bottom-center')"></div><!-- Asia Pacific (Hong Kong) -->
                        <div id="ap-west-1" class="region-new" onclick="getRegionInfo('ap-west-1','bottom-center')"></div><!-- Asia Pacific (Bahrain) -->
                        <div id="ap-south-1" class="region" onclick="getRegionInfo('ap-south-1','bottom-center')"></div><!-- Asia Pacific (Mumbai) -->
                        <div id="sa-east-1" class="region" onclick="getRegionInfo('sa-east-1','bottom-center')"></div><!-- South America (Sao Paulo) -->
                        <div id="cn-north-1" class="region" onclick="getRegionInfo('cn-north-1','bottom-center')"></div><!-- China (Beijing) -->
                        <div id="cn-northwest-1" class="region" onclick="getRegionInfo('cn-northwest-1','bottom-center')"></div><!-- China (Beijing) -->
                        <div id="bottom-right">
                                <p>
<!--                                   	<span class="block">Web Address:</span> <span id="hostname"></span><br />  -->
                                        <strong>Loaded:</strong> <?php echo getCurrentLocalTime(); ?>
                                </p>
				<table id="region" style="visibility: hidden;" border="1">
					<tr>
						<th>Code</th>
						<th>Name</th>
						<th>AZ's</th>
					</tr>
					<tr id="regionInfo"></tr>
				</table>
                        </div>

			<div id="us-east"></div>
                        <div id="us-west"></div>
			<div id="eu-central"></div>
			<div id="ap-north"></div>
			<div id="bottom-center"></div>
                </div>
		<!-- <?php getRegionsTable(); ?> -->
        </body>
</html>

