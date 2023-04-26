<?php /* ADD FRAMEWORK SVG */
function framework_shortcode() {
  ob_start();
  ?>
<style>
    .framework {
        max-width: 850px;
        margin: -60px auto;
    }
    .bar {
        cursor: pointer;
        pointer-events: all;
    }
    #culture a {
        cursor: pointer;
    }
    #culture .framework-tooltip, #environment .framework-tooltip, #infrastructure .framework-tooltip, #policy .framework-tooltip, #staff .framework-tooltip, #family .framework-tooltip, #quality .framework-tooltip, #innovation .framework-tooltip {
        visibility: hidden
    }
    #culture:hover .framework-tooltip, #culture:active .framework-tooltip, #environment:hover .framework-tooltip, #infrastructure:hover .framework-tooltip, #policy:hover .framework-tooltip, #staff:hover .framework-tooltip, #family:hover .framework-tooltip, #quality:hover .framework-tooltip, #innovation:hover .framework-tooltip {
        visibility: visible;
    }
    #culture .shade, #environment .shade, #infrastructure .shade, #policy .shade, #staff .shade, #family .shade, #quality .shade, #innovation .shade {
        visibility: hidden;
    }
    #culture:hover .shade, #environment:hover .shade, #infrastructure:hover .shade, #policy:hover .shade, #staff:hover .shade, #family:hover .shade, #quality:hover .shade, #innovation:hover .shade {
        visibility: visible;
    }
    .framework-tooltip text {
        fill: black;
        font-size: 12px;
        font-family: sans-serif;
    }
    .framework-tooltip rect {
        fill: #FFFFFF;
        stroke: #3a4f92;
    }
    .light rect {
        stroke: #1794ce;
    }
    .shade rect {
        fill: #ffffff;
    }
    .mobile-button {
        display: none;
        width: 250px;
        color: #fff;
        padding: 8px 35px;
        background: #9ac63f;
        border-radius: 5px;
        margin: 0 auto;
        font-weight: 600;
        font-size: 18px;
        text-align: center;
    }
    .mobile-button:hover {
        background: #666;
        color: #fff;
    }
    
    @media screen and (max-width: 768px) {
    .framework {
        margin: 0 auto;
    }
    .mobile-button {
        display: block;
    }
    }
</style>

<div class="framework">
    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 800 600" id="framework">
        <image width="800" height="550" xlink:href="https://berylinstitdev.wpengine.com/wp-content/uploads/2023/02/framework.svg"> </image>
        <g id="culture">
            <g class="shade" transform="translate(374,130)" opacity=".7">
                <rect rx="70" width="55" height="55"></rect>
            </g>
            <a href="https://www.theberylinstitute.org/page/Ecosystem-CultureLeadership" tabindex="0">
                <rect x="350" y="90" fill="pink" opacity="0" width="100" height="100" class="bar culture culture-trigger" id="content"></rect>
            </a> <a href="https://www.theberylinstitute.org/page/Ecosystem-CultureLeadership" tabindex="0">
                <rect x="400" y="70" fill="blue" opacity="0" width="350" height="60" class="bar culture culture-trigger" id="content"></rect>
            </a>
            <g class="framework-tooltip light" transform="translate(300,235)" opacity="0.95">
                <rect rx="5" width="200" height="100"></rect>
                <text>
                    <tspan x="10" y="20" dy="0" style="font-weight:bold; fill: #1794ce;">Culture &amp; Leadership</tspan>
                    <tspan x="10" y="40" dy="0">The foundation of any successful</tspan>
                    <tspan x="10" y="55" dy="0">experience effort is set on who an </tspan>
                    <tspan x="10" y="70" dy="0">organization is, its purpose and </tspan>
                    <tspan x="10" y="85" dy="0">values, and how it is led.</tspan>
                </text>
            </g>
        </g>
        <g id="environment">
            <g class="shade" transform="translate(374,386)" opacity=".7">
                <rect rx="70" width="55" height="55"></rect>
            </g>
            <a href="https://www.theberylinstitute.org/page/Ecosystem-EnvironmentHospitality" tabindex="0">
                <rect x="350" y="370" fill="pink" opacity="0" width="100" height="100" class="bar environment environment-trigger" id="content"></rect>
            </a> <a href="https://www.theberylinstitute.org/page/Ecosystem-EnvironmentHospitality" tabindex="0">
                <rect x="100" y="450" fill="blue" opacity="0" width="350" height="60" class="bar environment environment-trigger" id="content"></rect>
            </a>
            <g class="framework-tooltip light" transform="translate(280,235)" opacity="0.95">
                <rect rx="5" width="253" height="115"></rect>
                <text>
                    <tspan x="10" y="20" dy="0" style="font-weight:bold; fill: #1794ce;">Environment &amp; Hospitality</tspan>
                    <tspan x="10" y="40" dy="0">The space in which a healthcare experience</tspan>
                    <tspan x="10" y="55" dy="0">is delivered and the practices implemented </tspan>
                    <tspan x="10" y="70" dy="0">to ensure a positive, comfortable and </tspan>
                    <tspan x="10" y="85" dy="0">compassionate encounter must be part of </tspan>
                    <tspan x="10" y="100" dy="0">every effort.</tspan>
                </text>
            </g>
        </g>
        <g id="quality">
            <g class="shade" transform="translate(280,166)" opacity=".7">
                <rect rx="70" width="55" height="55"></rect>
            </g>
            <a href="https://www.theberylinstitute.org/page/Ecosystem-QualityClinicalExcellence" tabindex="0">
                <rect x="250" y="135" fill="pink" opacity="0" width="100" height="100" class="bar quality quality-trigger"></rect>
            </a> <a href="https://www.theberylinstitute.org/page/Ecosystem-QualityClinicalExcellence" tabindex="0">
                <rect x="0" y="100" fill="blue" opacity="0" width="350" height="120" class="bar quality quality-trigger"></rect>
            </a>
            <g class="framework-tooltip" transform="translate(285,235)" opacity="0.95">
                <rect rx="5" width="243" height="115"></rect>
                <text>
                    <tspan x="10" y="20" dy="0" style="font-weight:bold; fill: #3a4f92;">Quality &amp; Clinical Excellence</tspan>
                    <tspan x="10" y="40" dy="0">Experience encompasses all an individual</tspan>
                    <tspan x="10" y="55" dy="0">encounters and the expectations they </tspan>
                    <tspan x="10" y="70" dy="0">have for safe, quality, reliable, and </tspan>
                    <tspan x="10" y="85" dy="0">effective care focused on positively</tspan>
                    <tspan x="10" y="100" dy="0">impacting health and well-being.</tspan>
                </text>
            </g>
        </g>
        <g id="infrastructure">
            <g class="shade" transform="translate(462,168)" opacity=".7">
                <rect rx="70" width="55" height="55"></rect>
            </g>
            <a href="https://www.theberylinstitute.org/page/Ecosystem-InfrastructureGovernance" tabindex="0">
                <rect x="450" y="135" fill="pink" opacity="0" width="100" height="100" class="bar infrastructure infrastructure-trigger"></rect>
            </a> <a href="https://www.theberylinstitute.org/page/Ecosystem-InfrastructureGovernance" tabindex="0">
                <rect x="450" y="130" fill="blue" opacity="0" width="300" height="95" class="bar infrastructure infrastructure-trigger"></rect>
            </a>
            <g class="framework-tooltip" transform="translate(290,235)" opacity="0.95">
                <rect rx="5" width="233" height="115"></rect>
                <text>
                    <tspan x="10" y="20" dy="0" style="font-weight:bold; fill: #3a4f92;">Infrastructure &amp; Governance</tspan>
                    <tspan x="10" y="40" dy="0">Effective experience efforts require both</tspan>
                    <tspan x="10" y="55" dy="0">the right structures and processes by</tspan>
                    <tspan x="10" y="70" dy="0">which to operate and communicate and </tspan>
                    <tspan x="10" y="85" dy="0">the formal guidance in place to ensure</tspan>
                    <tspan x="10" y="100" dy="0">sustained strategic focus.</tspan>
                </text>
            </g>
        </g>
        <g id="policy">
            <g class="shade" transform="translate(462,348)" opacity=".7">
                <rect rx="70" width="55" height="55"></rect>
            </g>
            <a href="https://www.theberylinstitute.org/page/Ecosystem-PolicyMeasurement" tabindex="0">
                <rect x="450" y="335" fill="pink" opacity="0" width="100" height="100" class="bar policy policy-trigger"></rect>
            </a> <a href="https://www.theberylinstitute.org/page/Ecosystem-PolicyMeasurement" tabindex="0">
                <rect x="450" y="350" fill="blue" opacity="0" width="300" height="95" class="bar policy policy-trigger"></rect>
            </a>
            <g class="framework-tooltip" transform="translate(285,235)" opacity="0.95">
                <rect rx="5" width="243" height="115"></rect>
                <text>
                    <tspan x="10" y="20" dy="0" style="font-weight:bold; fill: #3a4f92;">Policy &amp; Measurement</tspan>
                    <tspan x="10" y="40" dy="0">Experience is driven and influenced by</tspan>
                    <tspan x="10" y="55" dy="0">external factors and systemic and financial</tspan>
                    <tspan x="10" y="70" dy="0">realities and requires accepted and </tspan>
                    <tspan x="10" y="85" dy="0">understood metrics to effectively measure </tspan>
                    <tspan x="10" y="100" dy="0">outcomes and drive action.</tspan>
                </text>
            </g>
        </g>
        <g id="innovation">
            <g class="shade" transform="translate(285,348)" opacity=".7">
                <rect rx="70" width="55" height="55"></rect>
            </g>
            <a href="https://www.theberylinstitute.org/page/Ecosystem-InnovationTechnology" tabindex="0">
                <rect x="250" y="335" fill="pink" opacity="0" width="100" height="100" class="bar innovation innovation-trigger"></rect>
            </a> <a href="https://www.theberylinstitute.org/page/Ecosystem-InnovationTechnology" tabindex="0">
                <rect x="0" y="340" fill="blue" opacity="0" width="300" height="110" class="bar innovation innovation-trigger"></rect>
            </a>
            <g class="framework-tooltip" transform="translate(280,235)" opacity="0.95">
                <rect rx="5" width="253" height="115"></rect>
                <text>
                    <tspan x="10" y="20" dy="0" style="font-weight:bold; fill: #3a4f92;">Innovation &amp; Technology</tspan>
                    <tspan x="10" y="40" dy="0">As a focus on experience expands, it </tspan>
                    <tspan x="10" y="55" dy="0">requires new ways of thinking and doing </tspan>
                    <tspan x="10" y="70" dy="0">and the technologies and tools to ensure </tspan>
                    <tspan x="10" y="85" dy="0">efficiencies, expand capacities and extend </tspan>
                    <tspan x="10" y="100" dy="0">boundaries of care.</tspan>
                </text>
            </g>
        </g>
        <g id="family">
            <g class="shade" transform="translate(243,258)" opacity=".7">
                <rect rx="70" width="55" height="55"></rect>
            </g>
            <a href="https://www.theberylinstitute.org/page/Ecosystem-PatientFamilyCommunityEngagement" tabindex="0">
                <rect x="220" y="235" fill="pink" opacity="0" width="100" height="100" class="bar family family-trigger"></rect>
            </a> <a href="https://www.theberylinstitute.org/page/Ecosystem-PatientFamilyCommunityEngagement" tabindex="0">
                <rect x="0" y="220" fill="blue" opacity="0" width="300" height="120" class="bar family family-trigger"></rect>
            </a>
            <g class="framework-tooltip light" transform="translate(275,235)" opacity="0.95">
                <rect rx="5" width="263" height="100"></rect>
                <text>
                    <tspan x="10" y="20" dy="0" style="font-weight:bold; fill: #1794ce;">Patient, Family &amp; Community Engagement</tspan>
                    <tspan x="10" y="40" dy="0">Central to any experience effort are the</tspan>
                    <tspan x="10" y="55" dy="0">voices of, contributions from and partnerships</tspan>
                    <tspan x="10" y="70" dy="0">with those receiving care and the community </tspan>
                    <tspan x="10" y="85" dy="0">served. </tspan>
                </text>
            </g>
        </g>
        <g id="staff">
            <g class="shade" transform="translate(503,258)" opacity=".7">
                <rect rx="70" width="55" height="55"></rect>
            </g>
            <a href="https://www.theberylinstitute.org/page/Ecosystem-StaffProvider" tabindex="0">
                <rect x="480" y="235" fill="pink" opacity="0" width="100" height="100" class="bar staff staff-trigger"></rect>
            </a> <a href="https://www.theberylinstitute.org/page/Ecosystem-StaffProvider" tabindex="0">
                <rect x="450" y="228" fill="blue" opacity="0" width="300" height="120" class="bar staff staff-trigger"></rect>
            </a>
            <g class="framework-tooltip light" transform="translate(285,235)" opacity="0.95">
                <rect rx="5" width="243" height="115"></rect>
                <text>
                    <tspan x="10" y="20" dy="0" style="font-weight:bold; fill: #1794ce;">Staff &amp; Provider Engagement</tspan>
                    <tspan x="10" y="40" dy="0">Caring for those delivering and supporting</tspan>
                    <tspan x="10" y="55" dy="0">the delivery of care and reaffirming a</tspan>
                    <tspan x="10" y="70" dy="0">connection to meaning and purpose is </tspan>
                    <tspan x="10" y="85" dy="0">fundamental to the successful realization</tspan>
                    <tspan x="10" y="100" dy="0">of a positive experience.</tspan>
                </text>
            </g>
        </g>
    </svg>
</div>
<?php
$output = ob_get_clean();
return $output;
}
add_shortcode( 'framework', 'framework_shortcode' );
?>