<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <!--
    Modified from the Debian original for Ubuntu
    Last updated: 2022-03-22
    See: https://launchpad.net/bugs/1966004
  -->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>CDW/Digital Velocity MaaS WebServer</title>
    <style type="text/css" media="screen">
  * {
    margin: 0px 0px 0px 0px;
    padding: 0px 0px 0px 0px;
  }

  body, html {
    padding: 3px 3px 3px 3px;

    background-color: #D8DBE2;

    font-family: Ubuntu, Verdana, sans-serif;
    font-size: 11pt;
    text-align: center;
  }
  img:hover {
    -webkit-transform: scale(1.5,1.5);
  }
  div.main_page {
    position: relative;
    display: table;

    width: 1600px;
   
    margin-bottom: 3px;
    margin-left: auto;
    margin-right: auto;
    padding: 0px 0px 0px 0px;

    border-width: 2px;
    border-color: #212738;
    border-style: solid;

    background-color: #FFFFFF;

    text-align: center;
  }

  div.page_header {
    height: 180px;
    width: 100%;

    background-color: #F5F6F7;
  }

  div.page_header span {
    margin: 15px 0px 0px 50px;

    font-size: 180%;
    font-weight: bold;
  }

  div.page_header img {
    margin: 3px 0px 0px 40px;

    border: 0px 0px 0px;
  }

  div.banner {
    padding: 9px 6px 9px 6px;
    background-color: #E9510E;
    color: #FFFFFF;
    font-weight: bold;
    font-size: 112%;
    text-align: center;
    position: absolute;
    left: 40%;
    bottom: 30px;
    width: 20%;
  }

  div.table_of_contents {
    clear: left;

    min-width: 200px;

    margin: 3px 3px 3px 3px;

    background-color: #FFFFFF;

    text-align: left;
  }

  div.table_of_contents_item {
    clear: left;

    width: 100%;

    margin: 4px 0px 0px 0px;

    background-color: #FFFFFF;

    color: #000000;
    text-align: left;
  }

  div.table_of_contents_item a {
    margin: 6px 0px 0px 6px;
  }

  div.content_section {
    margin: 3px 3px 3px 3px;

    background-color: #FFFFFF;

    text-align: left;
  }

  div.content_section_text {
    padding: 4px 8px 4px 8px;

    color: #000000;
    font-size: 100%;
  }

  div.content_section_text pre {
    margin: 8px 0px 8px 0px;
    padding: 8px 8px 8px 8px;

    border-width: 1px;
    border-style: dotted;
    border-color: #000000;

    background-color: #F5F6F7;

    font-style: bold;
  }

  div.content_section_text p {
    margin-bottom: 6px;
    width: 100%;
  }

  div.content_section_text ul, div.content_section_text li {
    padding: 4px 8px 4px 16px;
  }

  div.section_header {
    padding: 3px 6px 3px 6px;

    background-color: #8E9CB2;

    color: #FFFFFF;
    font-weight: bold;
    font-size: 112%;
    text-align: center;
    width: 100%;
  }

  div.section_header_grey {
    background-color: #9F9386;
  }

  .floating_element {
    position: relative;
    float: left;
  }

  div.table_of_contents_item a,
  div.content_section_text a {
    text-decoration: none;
    font-weight: bold;
  }

  div.table_of_contents_item a:link,
  div.table_of_contents_item a:visited,
  div.table_of_contents_item a:active {
    color: #000000;
  }

  div.table_of_contents_item a:hover {
    background-color: #000000;

    color: #FFFFFF;
  }

  div.content_section_text a:link,
  div.content_section_text a:visited,
   div.content_section_text a:active {
    background-color: #DCDFE6;

    color: #000000;
  }

  div.content_section_text a:hover {
    background-color: #000000;

    color: #DCDFE6;
  }

  div.validator {
  }
    </style>
  </head>
  <body>
    <div class="main_page">
      <div class="page_header floating_element">
        <img src="icons/cdw-computer-centers-logo-png-transparent.png" alt="CDW Logo"
             style="width:184px;height:146px;" class="floating_element" />
        <div>
          <span style="margin-top: 1.5em;" class="floating_element">
            CDW/DV MaaS Server - MaaS/PXE Process Diagram 
           
          </span>
        </div>
        
       
      </div>
      <div class="content_section floating_element">
        <div class="content_section_text">
          <p>
                This is the default welcome page used to show that you have reached
                the CDW/Digital Velocity MaaS Webserver used to load OS images.
                
                If you can read this page, it means that the Apache HTTP server is working
                -- and the OS images are installed at <a target="_blank" href="http://<?php echo $_SERVER['SERVER_ADDR']; ?>:8080">MaaS WebServer</a>. All hosted OS images  
                are listed there.
          </p>

      
        </div>
        <div class="section_header">
          <div id="changes"></div>
                Configuration Overview
        </div>
        <div class="content_section_text">
          <p>
                The CDW/DV MaaS Server
          </p>
          <p>
                The MaaS server is built from the following components
          </p>
<pre>
-- The DNSMASQ Service
  -- DNSMASQ provides DHCP services that focus strictly on PXE requests
    -- DNSMASQ/DHCP will respond to L2 DHCP requests OR requests that are fowarded.  
      -- Forwarded requests are assumed to for boot service information only:  
      -- DNSMASQ will not assign IP addresses to forwarded requests.
    -- DNSMASQ/TFTP services
     -- TFTP is leveraged only for initial loading of the of the iPXE firmware
  -- iPXE
    -- iPXE is the firmware of choice.
    -- All machines are "handed" the iPXE firmware and chainloaded into that firmware. 
    -- From there a decision is made:  Is this authorized (via MAC address) or not.
        -- Authorized machines are then instructed to load the appropriate OS (via a chainloaded iPXE script),OR
        -- Unauthorized machines are loaded with an iPXE menu that provides multiple "management" options (including querying the system hardware itself)
          -- There is a 5 minute timer on the menu, at which point the machine is rebooted and re-cycles.   The intent is to put the machine into a cycle of checking for instructions.
-- Apache/HTTP
  -- HTTP is leveraged for all binary and script transport post the initial TFTP load of the iPXE firmware. 
  </pre>   
          <p>
                The file system layout for the CDW/DV MaaS Service:
          </p>
<pre>
/etc/apache2/
|-- apache2.conf
|       --  ports.conf (port 8080 enabled)
|       -- /pxeboot/ (allow file indexing)
|       -- /mnt/pxe-os/ (allow file indexing)
|-- conf-enabled
|       -- 000-pxeboot.conf
|-- sites-enabled
|       -- 000-pxeboot.conf
/pxeboot/
|-- firmware (the ipxe boot files)
|-- ipxe-build-scripts (where ipxe looks for authorized machine build directives)
|-- ipxe-build-templates (where we keep the templates used to make the build directives)
|-- ipxe-config (the ipxe scripts that make it all happen)
|-- os-images/
|---- /ISOs (contains the OS installation ISOs)
|---- /{ISO mount point folders. eg., centos7, centos8, etc.}
</pre>



        <div class="section_header">
            <div id="docroot"></div>
                The PXE Boot Process
        </div>

        <div class="content_section_text">
            <p>
                The PXE booting process progresses in accordance with the following flowchart (hover to enlarge):
            </p>
            <img src="/docs/maas-architecture-ipxe-process.png" alt="CDW Logo"
                 style="width:100%" />
        </div>

        <div class="section_header">
          <div id="bugs"></div>
                MaaS Process
        </div>
        <div class="content_section_text">
<p>
<pre>
-- Create a build script template for CentOS-7 in /pxeboot/ipxe-build-templates (ie. centos7-ipxe-build-template.ipxe)
---- These can be likely automated, but likely will require hand seeding at first
---- The build script is am iPXE script that loads the kernel file and any options, the initrd file, and then performs a boot.
---- The boot params must include a pointer to the HTTP server for the actual .iso root so that the install can happen 

-- download the ISO of choice to /pxeboot/os-images/IOSs
-- create an empty folder that represents the IOS /pxeboot/os-images.  This folder is the mount point for the ISO
---- Example:  For "CentOS-7-x86_64-Minimal-2009.ios" create a blank folder called "centos7"
--Create a new entry in /etc/fstab for the ISO
---- Example:  /pxeboot/os-images/ISOs/CentOS-7-x86_64-Minimal-2009.iso /pxeboot/os-images/centos7 iso9660 loop,auto,ro 0 0
-- mount /pxeboot/os-images/centos7


-- Get the target machines raw MAC address (ie. No dashes or colons)
-- Copy the centos7-ipxe-build-template in /pxeboot/ipxe-build-templates to /pxeboot/ipxe-build-scripts/mac-{raw MAC address}.ipxe file
-- Power up the target machine
                </pre>
              </p>
          <p>
                The entire MaaS process is shown in the diagram below:.
          </p>

        </div>

      </div>
    </div>
    <div class="validator">
    </div>
  </body>
</html>
