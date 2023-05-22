# maas-server-files
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

The file system layout for the CDW/DV MaaS Service:

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