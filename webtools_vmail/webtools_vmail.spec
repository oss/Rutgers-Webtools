%define prefix /usr/local
%define _libdir %{prefix}/lib64

Name:		webtools_vmail
Version:	2.0
Release:	4.ru6
Summary:	vmail webtool for Rutgers
Group:		Services
License:	GPL
BuildRoot:	%{_tmppath}/%{name}-%{version}-%{release}-root
Source0:	http://jla.rutgers.edu/~brylon/Wget/webtools/linux/webtools_vmail-%version.tgz

Requires: webtools >= 2.0, python-inotify, samba-common, postfix

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
mkdir -p 0555 $RPM_BUILD_ROOT%{_libdir}/webtools/html/vmail
mkdir -p 0555 $RPM_BUILD_ROOT%{_libdir}/webtools/webbin

cp -R $RPM_BUILD_DIR/%{name}-%{version}/config/* %{buildroot}%{_libdir}/webtools/config
cp -R $RPM_BUILD_DIR/%{name}-%{version}/html/* %{buildroot}%{_libdir}/webtools/html/vmail
cp -R $RPM_BUILD_DIR/%{name}-%{version}/webbin/* %{buildroot}%{_libdir}/webtools/webbin

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr(644, root, root, 755)
%doc README Changelog Todo 
%{_libdir}/webtools/html/vmail/index.php
%{_libdir}/webtools/html/vmail/myfunctions.php
%{_libdir}/webtools/html/vmail/my.js
%{_libdir}/webtools/html/vmail/showresults.php
%{_libdir}/webtools/config/vmail_config.php.sample
%attr(0555, root, root) %{_libdir}/webtools/webbin/vmail

%changelog
* Mon Jan 09 2012 Josh Matthews <jam761@nbcs.rutgers.edu> 2.00-4.ru6
- rebuit for centos 6

* Tue Nov 29 2011 Phillip Quiza <pquiza@nbcs.rutgers.edu> 2.00-4.ru
- changed perms for /usr/local/lib64/webtools/webbin/vmail from 550 to 555
- updated source

* Tue Nov 15 2011 Phillip Quiza <pquiza@nbcs.rutgers.edu> 2.00-3.ru
- changed perms for /usr/local/lib64/webtools/webbin/vmail from 500 to 555

* Fri Oct 28 2011 Phillip Quiza <pquiza@nbcs.rutgers.edu> 2.00-2.ru
- changed %{_libdir} from /usr/lib64 to /usr/local/lib64
- removed /usr/local/lib64/webtools/webbin/vmail directory

* Thu Oct 20 2011 Jarek Sedlacek <jarek@nbcs.rutgers.edu> 2.00-1.ru
- Initial Build
