%define prefix /usr/local
%define _libdir %{prefix}/lib64

Name:		webtools_dbaudit
Version:	2.0
Release:	6.ru6
Summary:	Audits DB for webtools
Group:		Services
License:	GPL
BuildRoot:	%{_tmppath}/%{name}-%{version}-%{release}-root
Source0:	http://jla.rutgers.edu/~brylon/Wget/webtools/linux/webtools_dbaudit-%version.tgz

Requires: webtools >= 2.0

%description
Core binaries, configs and templates for many Rutgers specific web applications (aka webtools). By default comes with the quota webtool to allow a user to check their quota via the web.

%prep
%setup -q

%build
## Nothing to build 

%install
rm -rf $RPM_BUILD_ROOT
mkdir -p 0755 $RPM_BUILD_ROOT/usr/share/doc/%{name}-%{version}
mkdir -p 0755 $RPM_BUILD_ROOT%{_libdir}/webtools/config
mkdir -p 0511 $RPM_BUILD_ROOT%{_libdir}/webtools/webbin
mkdir -p 0555 $RPM_BUILD_ROOT%{_libdir}/webtools/html/dbaudit

cp -R $RPM_BUILD_DIR/%{name}-%{version}/config/* %{buildroot}%{_libdir}/webtools/config
cp -R $RPM_BUILD_DIR/%{name}-%{version}/webbin/* %{buildroot}%{_libdir}/webtools/webbin
cp -R $RPM_BUILD_DIR/%{name}-%{version}/html/* %{buildroot}%{_libdir}/webtools/html/dbaudit

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr(644, root, root, 755)
%doc README Changelog
%{_libdir}/webtools/config/dbaudit_config.php.sample
%attr(511,root,apache) %dir %{_libdir}/webtools/webbin/
%attr(555,root,root) %{_libdir}/webtools/webbin/dbaudit
%{_libdir}/webtools/html/dbaudit/index.php
%{_libdir}/webtools/html/dbaudit/myfunctions.php

%changelog
* Mon Jan 09 2012 Josh Matthews <jam761@nbcs.rutgers.edu> - 2.0-6.ru6
- rebuilt for centos 6

* Tue Nov 29 2011 Phillip Quiza <pquiza@nbcs.rutgers.edu> - 2.0-6.ru
- updated source

* Fri Oct 28 2011 Phillip Quiza <pquiza@nbcs.rutgers.edu> - 2.0-5.ru
- source update
- fixed perms on /usr/local/lib64/webtools/webbin/dbaudit
- changed %{_libdir} from /usr/lib64 to /usr/local/lib64

* Mon Sep 19 2011 Jarek Sedlacek <jarek@nbcs.rutgers.edu> - 2.0-4.ru
- rebuilt with updated source 
* Thu Sep 15 2011 Jarek Sedlacek <jarek@nbcs.rutgers.edu> - 2.0-3.ru
- fixed perms on /usr/lib64/webtools/webbin/dbaudit
* Thu Aug 11 2011 Steven Lu <sjlu@nbcs.rutgers.edu> - 2.0-2.ru
- added /usr/share/doc/webtools_dbaudit-2.0/Changelog                                                                                                                            - removed html/my.js file from zarball 
- update perms to be 0555 for /usr/lib64/webtools/webbin/dbaudit

* Tue Aug 02 2011 Phillip Quiza <pquiza@nbcs.rutgers.edu> 
- Initial build for Rutgers CentOS
