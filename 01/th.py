from ipaddress import ip_network

with open("th.zone") as f:
    lines = f.readlines()

for i, line in enumerate(lines, 1):
    net = ip_network(line.strip())
    ip = str(net.network_address)
    mask = str(net.netmask)
    name = f"TH_IP_{i:03d}"
    print(f"address-object {name} subnet {ip} {mask}")