#! /bin/sh
ip=$1
gw=$2
nm=$3
domain=$4
dns1=$5
dns2=$6


cat << EOF > /tmp/interfaces.temp
auto lo
iface lo inet static

auto eth0
iface eth0 inet static
netmask $nm
address $ip
gateway $gw

auto p5p1
iface p5p1 inet static
netmask 255.0.0.0
address 1.1.1.1
EOF
 

cat << EOF > /etc/resolv.conf
search $domain
nameserver $dns1
nameserver $dns2
EOF


sudo cp /tmp/interfaces.temp /etc/network/interfaces
sudo rm -f /tmp/interfaces.temp
sudo ifdown eth0 
sudo ifup eth0
# sudo service apache2 restart
