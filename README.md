# maas-server-files
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
  /opt/pxeboot/
  |-- firmware (the ipxe boot files)
  |-- ipxe-build-scripts (where ipxe looks for authorized machine build directives)
  |-- ipxe-build-templates (where we keep the templates used to make the build directives)
  |-- ipxe-config (the ipxe scripts that make it all happen)
  /opt/os-images/
  |---- /ISOs (contains the OS installation ISOs)
  |---- /{ISO mount point folders. eg., centos7, centos8, etc.}
  /var/www/html
  |-- This documentation

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

The PXE Boot Server Build Process
<pre>
-- Ubuntu 22.10
	Live DVD
-- Recommended Server install


Login as user:
Userid:pxe
Passed: NetApp!23

sudo apt update
sudo apt upgrade


~/mkdir temp
cd temp
# grab the latest PXEBOOT server config files
git clone https://github.com/steve-jones62/maas-server-files.git


###########################################################################################
# Installation of PXEBOOT files
############################################################################################
cd /opt

sudo cp -r ~/temp/maas-server-files/pxeboot .
sudo chown -R iac:iac pxeboot

sudo cp -r ~/temp/maas-server-files/os-images .
sudo chown -R iac:iac os-images 

Copy all needed .iso's to this directory
cd ..
Ensure that there is a folder for each needed .iso
Mount each .iso at /opt/os-images/{os-name}  (HINT:  use short names for the mount points.  Examples are already provided eg., centos7, centos8, etc.)

**Example: ** 	mount -o loop -t iso9660 --read-only /opt/ISO/CentOS-Stream-8-boot.iso /opt/os-images/centos8/
		mkdir /mnt/pxe-os
		mount -o loop -t iso9660 --read-only /opt/ISO/CentOS-Stream-8-boot.iso	/mnt/pxe-os
cd /opt/pxeboot/ipxe-config
Edit boot.ipxe.cfg and update the following:

---------------------------------------------------------
# These 2 params MUST change based on the host server
---------------------------------------------------------
set http-server-ip 192.168.128.26:8080
set http-server-name hpc-pxe-2u-clone:8080
set https-server-ip 192.168.128.26:443


###########################################################################################
# Installation of DNSMASQ requires turning off the resolver stub and replacing resolve.conf
############################################################################################
sudo systemctl disable system-resolved
sudo systemctl stop systems-resolved
sudo unlink /etc/resolv.conf

# NOTE: A nameserver directive needs to be created for each required nameserver.  This example refrenses Google DNS
echo nameserver 8.8.8.8 | sudo tee /etc/resolv.conf

# Install DNSMASQ
sudo apt install dnsmasq

cd /etc
sudo cp dnsmasq.conf dnsmasq.conf.sav
sudo cp ~/temp/maas-server-files/etc/dnsmasq.conf /etc/dnsmasq.conf
Edit dnsmasq.conf

------------------------------------------------------------------
--- change these lines to match the IP address info for your site
--- This example is of a server with a non-routed and routed interface.  x.y.184.z is the private L2 subnet, x.y.128.z is the routed
------------------------------------------------------------------
# listen on PXEBOOT interface/vlan
listen-address=192.168.184.100
interface=ens38


# DHCP range 10.0.0.200 ~ 10.0.0.250
dhcp-range=192.168.184.50,192.168.184.99,255.255.255.0,24h

# Default gateway
dhcp-option=3,192.168.184.100

# Domain name - homelab.net
dhcp-option=15,kupjones.com

# Broadcast address
dhcp-option=28,192.168.184.255

dhcp-boot=tag:!ipxe,tag:BIOS,firmware/undionly.kpxe,,192.168.184.156    <------- This is your tftp server address (this server -- it has 2 interfaces)
dhcp-boot=tag:!ipxe,tag:!BIOS,firmware/ipxe.efi,,192.168.184.156        <-------- ibid
dhcp-boot=tag:ipxe,http://192.168.128.24:8080/ipxe-config/boot.ipxe     <-------- This is your web server (it can be this same server - this one has 2 interfaces)

# ------------------------------------------
-- Now restart dnsmasq
sudo systemctl restart dnsmasq

	Troublehsooting:
	chmod 777 /opt/
	sudo chown iac:iac /opt/

------ TEST:  sudo systemctl status dnsmasq --------


###########################################################################################
# Installation of Apache
############################################################################################

sudo apt install apache2	#Skipped
sudo apt install php		#skipped
cd /var/www/html
sudo mv index.html index.html.sav
sudo cp -r ~/temp/maas-server-files/var/www/html/* .


---------------------
-- update the apache2 config files

cd /etc/apache2
sudo mv apache2.conf apache2.conf.sav
sudo mv ports.conf ports.conf.sav
sudo cp ~/temp/maas-server-files/etc/apache2/apache2.conf .
sudo cp ~/temp/maas-server-files/etc/apache2/ports.conf .
sudo cp ~/temp/maas-server-files/etc/apache2/sites-available/000-pxeboot.conf ./sites-available/
cd sites-enabled
sudo ln -s ../sites-available/000-pxeboot.conf .

sudo systemctl restart apache2 




-----------------------------------------
- Testing
-----------------------------------------

wget {server ip} ---------------> documentation page
wget {server ip}:8080 ----------> file list of /pxboot root
sudo tail -f /var/log/dnsmasq.log
sudo tail -f /var/log/apache2/pxeboot-access.log 


</pre>
