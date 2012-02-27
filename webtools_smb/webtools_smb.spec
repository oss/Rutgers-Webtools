%define prefix /usr/local
%define _libdir %{prefix}/lib64

Name:		webtools_smb
Version:	2.0
Release:	5.ru6
Summary:	SMB webtool for Rutgers
Group:		Services
License:	GPL
BuildRoot:	%{_tmppath}/%{name}-%{version}-%{release}-root
Source0:	http://jla.rutgers.edu/~brylon/Wget/webtools/linux/webtools_smb-%version.tgz

Requires: webtools >= 2.0, python-inotify, samba-common, postfix

BuildArch: noarch

%description
Core binaries, configs and templates for many Rutgers specific web applications (aka webtools). By default comes with the quota webtool to allow a user to check their quota via the web.

%prep
%setup -q

%build
## Nothing to build 

%install
rm -rf $RPM_BUILD_ROOT
mkdir -p 0755 $RPM_BUILD_ROOT/usr/share/doc/%{name}-%{version}
mkdir -p 0755 $RPM_BUILD_ROOT/usr/local/
mkdir -p 0555 $RPM_BUILD_ROOT%{_libdir}/webtools/config
mkdir -p 0555 $RPM_BUILD_ROOT%{_libdir}/webtools/html/smb
mkdir -p $RPM_BUILD_ROOT/etc/init.d

cp -R $RPM_BUILD_DIR/%{name}-%{version}/config/* %{buildroot}%{_libdir}/webtools/config
cp -R $RPM_BUILD_DIR/%{name}-%{version}/html/* %{buildroot}%{_libdir}/webtools/html/smb
cp -R $RPM_BUILD_DIR/%{name}-%{version}/smbacctd %{buildroot}/usr/local/
cp -R $RPM_BUILD_DIR/%{name}-%{version}/smbacctd/smbacctd.init %{buildroot}/etc/init.d/smbacctd
rm %{buildroot}/usr/local/smbacctd/smbacctd.init

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr(644, root, root, 755)
%doc README Changelog
#%{_libdir}/webtools/config/smb_config.php.sample
#%{_libdir}/webtools/html/smbacctd/*
#/usr/local/smbacctd/util/*
#/etc/init.d/smbacctd
/etc/init.d/smbacctd
/usr/local/smbacctd/smbacctd.conf.sample
/usr/local/smbacctd/smbacctd
/usr/local/smbacctd/util/
/usr/local/smbacctd/util/__init__.py
/usr/local/smbacctd/util/system.py
/usr/local/smbacctd/util/config.py
/usr/local/smbacctd/util/security.py
%{_libdir}/webtools/config/smb_config.php.sample
%{_libdir}/webtools/html/smb/index.php
%{_libdir}/webtools/html/smb/showresult.php
%{_libdir}/webtools/html/smb/my.js

%changelog
* Tue Nov 29 2012 Josh Matthews <jam761@nbcs.rutgers.edu> - 2.00-5.ru6
- rebuilt for centos 6

* Tue Nov 29 2011 Phillip Quiza <pquiza@nbcs.rutgers.edu> - 2.00-5.ru
- updated source

* Fri Oct 28 2011 Phillip Quiza <pquiza@nbcs.rutgers.edu> - 2.00-4.ru
- updated source
- changed %{_libdir} from /usr/lib64 to /usr/local/lib64

* Tue Oct 04 2011 Jarek Sedlacek <jarek@nbcs.rutgers.edu> - 2.00-3.ru
- Version bump
* Mon Sep 19 2011 Russ Frank <rfranknj@nbcs.rutgers.edu> - 2.00-2.ru
- BuildArch: noarch

* Thu Sep 15 2011 Jarek Sedlacek <jarek@nbcs.rutgers.edu> 2.00-1.ru
- Initial Build
